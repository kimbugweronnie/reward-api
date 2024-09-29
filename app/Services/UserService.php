<?php
namespace App\Services;
use App\Models\User;
use App\Models\Merchant;
use App\Models\Program;
use App\Models\Customer;
use App\Models\UserVerfiy;
use App\Models\Subscription;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Requests\UserLoginRequest;

use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use App\Http\Resources\LoginResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\CustomerLoginResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Mail;

class UserService extends Controller
{
    private $user;
    private $customer;
    private $userverify;
    private $subscription;
    private $program;
    private $merchant;
    public $role;

    public function __construct(User $user, Role $role, UserVerfiy $userverify, Customer $customer, Subscription $subscription, Program $program, Merchant $merchant)
    {
        $this->user = $user;
        $this->role = $role;
        $this->userverify = $userverify;
        $this->customer = $customer;
        $this->subscription = $subscription;
        $this->program = $program;
        $this->merchant = $merchant;
    }

    public function registration($request, $phone_number)
    {
        $user = $this->user->createUser($request, $phone_number);

        $role1 = Role::where('name', $request['user_type'])->first();
        $user->assignRole($role1);
        $userverify = $this->userverify->create_record($user->id);
        $url = url($userverify->token);
        // Mail::to($user->email)->send(new EmailVerificationMail($url));
        return $this->sendResponse(new UserResource($user), 201);
    }

    public function login(UserLoginRequest $request)
    {
        return $this->authCheck($request->email, $request->password);
    }

    public function authCheck($email, $password)
    {
        $user = $this->user->userbyemail($email);
        if (!$user) {
            return $this->messageSubscription('Wrong Credentials', 401);
        }
        if (Auth::attempt(['email' => $email, 'password' => $password]) == 0) {
            return $this->messageSubscription('Wrong Password', 401);
        } else {
            return $this->createToken($email);
        }
    }

    public function createToken($email)
    {
        $programs = $this->program->programs();
        $now = strtotime(date('Y-m-d H:i:s'));
        foreach ($programs as $program) {
            $expirytime = strtotime($program->due_date);
            if ($now > $expirytime) {
                $this->program->deactivateProgram($program->id);
                $this->program->expireProgram($program->id);
            }
        }
        $user = $this->user->userByemail($email);
        $customer = $this->customer->customerUser($user['id']);
        $merchant = $this->merchant->merchantUser($user['id']);
        $accesstoken = auth()
            ->user()
            ->createToken('Anything', ['*'], now()->addMinutes(240), $user->id, 'App\\Models\\User');

        $user->access_token = $accesstoken->plainTextToken;
        $user->expires_in = 3600;
        if ($customer) {
            $userdetails = [
                'id' => $user->id,
                'name' => $customer->first_name . ' ' . $customer->last_name,
                'email' => $user->email,
                'user_type' => $merchant ? $merchant->user_type : $customer->user_type,
            ];
            return $this->loginResponse($user->access_token, $userdetails, $user->expires_in, 201);
        } elseif ($merchant) {
            $userdetails = [
                'id' => $user->id,
                'name' => $merchant->merchant_name,
                'email' => $user->email,
                'user_type' => $merchant ? $merchant->user_type : $customer->user_type,
            ];
            return $this->loginResponse($user->access_token, $userdetails, $user->expires_in, 201);
        } else {
            return $this->messageSubscription('Wrong Credentials', 401);
        }
    }

    public function getPrograms($id)
    {
        $programs = [];
        $subscriptionprograms = $this->subscription->userSubscriptions($id);
        foreach ($subscriptionprograms as $program) {
            $userprogram = $this->program->program($program['program_id']);
            array_push($programs, $userprogram);
        }
        return $programs;
    }

    public function getMerchants($id)
    {
        $merchantids = [];
        $usermerchants = [];
        $subscriptionprograms = $this->subscription->userSubscriptions($id);
        foreach ($subscriptionprograms as $program) {
            $userprogram = $this->program->program($program['program_id']);
            array_push($merchantids, $userprogram->merchant_id);
        }
        $merchants = array_unique($merchantids);
        foreach ($merchants as $merchant) {
            array_push($usermerchants, $this->merchant->merchant($merchant));
        }
        return $usermerchants;
    }

    public function getCustomers()
    {
        $role = 'customer';
        return $this->sendResponse($this->user->customers($role), 200);
    }

    public function getCustomer($id)
    {
        $user = $this->user->customer($id);
        return $this->sendResponse(new CustomerResource($user), 200);
    }

    public function updateCustomer($request, $id)
    {
        $user = $this->user->customer($id);
        $user->update($request->validated());
        return $this->sendResponse(new CustomerResource($user), 200);
    }

    public function destroy($id)
    {
        $this->user::destroy($id);
        return $this->messageSubscription('User has been deleted', 200);
    }
}
