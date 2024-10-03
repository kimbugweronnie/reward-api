<?php
namespace App\Services;
use App\Models\User;
use App\Models\Merchant;
use App\Models\Program;
use App\Models\Customer;
use App\Models\UserVerify;
use App\Models\Subscription;
use Spatie\Permission\Models\Role;
use App\Http\Requests\UserLoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;


class UserService extends Controller
{
    private $user;
    private $customer;
    private $userverify;
    private $subscription;
    private $program;
    private $merchant;
    public $role;

    public function __construct(User $user, Role $role, UserVerify $userverify, Customer $customer, Subscription $subscription, Program $program, Merchant $merchant)
    {
        $this->user = $user;
        $this->role = $role;
        $this->userverify = $userverify;
        $this->customer = $customer;
        $this->subscription = $subscription;
        $this->program = $program;
        $this->merchant = $merchant;
    }

    /**
     * Handle user registration
     */
    public function registration($request)
    {
        $user = $this->createUser($request);
        $role = $this->fetchRole($request);
        $userVerify = $this->createUserVerify($user);
        $user->assignRole($role);

        return $this->sendResponse(new UserResource($user), 201);
    }

    /**
     * Create a new user
     */
    public function createUser($request)
    {
        return $this->user->create([
            'email' => $request['email'],
            'mobile' => $request['phone_number'],
            'phone_prefix' => $request['phone_prefix'],
            'password' => bcrypt($request['password']),
        ]);
    }

    /**
     * Create verification record for a user
     */
    public function createUserVerify($user)
    {
        $userverify = $this->userverify->create(['user_id' => $user->id, 'token' => Str::random(60)]);
        $this->sendVerificationEmail($userverify);
        return $userverify;
    }

    /**
     * Fetch the role for a user
     */
    public function fetchRole($user_type)
    {
        return Role::where('name', $user_type)->first();
    }

    /**
     * Handle login request
     */
    public function login($request)
    {
        return $this->authenticate($request->email, $request->password);
    }

    /**
     * Authenticate user credentials
     */
    public function authenticate($email, $password)
    {
        if (!$user = $this->attemptLogin($email, $password)) {
            return $this->messageSubscription('Wrong credentials', 401);
        }

        return $this->generateAccessToken($user);
    }

    /**
     * Attempt to log the user in
     */
    public function attemptLogin($email, $password)
    {
        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            return null;
        }
        return $this->user->where('email', $email)->first();
    }

    /**
     * Generate access token for the authenticated user
     */
    public function generateAccessToken($user)
    {
        $tokenResult = $user->createToken('AccessToken', ['*']);
        $details = $this->getCustomerDetails($user->id);
        return [
            'token' => $tokenResult->plainTextToken,
            'details' => $details
        ];
    }

    /**
     * Fetch user details by email
     */
    private function userByEmail($email)
    {
        return $this->user->where('email', $email)->first();
    }

    /**
     * Fetch customer details for a user
     */
    public function getCustomerDetails($userId)
    {
        $customer = $this->customer->customerUser($userId);
        $merchant = $this->merchant->merchantUser($userId);
        if ($customer) {
            return $this->prepareCustomerResponse($customer);
        } elseif ($merchant) {
            return $this->prepareMerchantResponse($merchant);
        } else {
            return $this->messageSubscription('User details not found', 404);
        }
    }

    /**
     * Prepare customer response
     */
    private function prepareCustomerResponse($customer)
    {
        return [
            'id' => $customer->id,
            'name' => $customer->first_name . ' ' . $customer->last_name,
            'email' => $customer->email,
            'user_type' => 'customer'
        ];
    }

    /**
     * Prepare merchant response
     */
    public function prepareMerchantResponse($merchant)
    {
        return [
            'id' => $merchant->id,
            'name' => $merchant->merchant_name,
            'user_type' => 'merchant'
        ];
    }

    /**
     * Get user programs
     */
    public function getPrograms($userId)
    {
        $programs = [];
        foreach ($this->subscriptionPrograms($userId) as $program) {
            $programs[] = $this->userProgram($program['program_id']);
        }
        return $programs;
    }

    /**
     * Fetch subscription programs for a user
     */
    private function subscriptionPrograms($userId)
    {
        return $this->subscription->userSubscriptions($userId);
    }

    /**
     * Fetch specific program for a user
     */
    public function userProgram($programId)
    {
        return $this->program->program($programId);
    }

    /**
     * Get user merchants
     */
    public function getMerchants($userId)
    {
        $merchantIds = [];
        $userMerchants = [];

        foreach ($this->subscriptionPrograms($userId) as $program) {
            $merchantIds[] = $this->userProgram($program['program_id'])->merchant_id;
        }

        foreach (array_unique($merchantIds) as $merchantId) {
            $userMerchants[] = $this->merchant->merchant($merchantId);
        }

        return $userMerchants;
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        $this->user->destroy($id);
        return $this->messageSubscription('User deleted successfully', 200);
    }
}
