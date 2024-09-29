<?php
namespace App\Services;
use App\Models\User;
use App\Models\Customer;
use App\Models\UserVerfiy;
use App\Models\Otp;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\CustomerRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Http\Requests\CustomerLoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\LoginResource;
use App\Http\Resources\SingleCustomerResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Exception;
use Twilio\Rest\Client;
// use App\Mail\EmailVerificationMail;
// use Illuminate\Support\Facades\Mail;

class CustomerService extends Controller
{
    private $user;
    private $otp;
    private $customer;

    public function __construct(User $user, Customer $customer, Otp $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
        $this->customer = $customer;
    }

    public function store($request)
    {
        try {
            $user = $this->user->createUser($request['email'], $request['phone_number'], $request['phone_prefix'], $request['password']);
            $customer = $this->customer->createCustomer($request['first_name'], $request['last_name'], $request['user_type'], $user['id']);
            $role = Role::where('name', $request['user_type'])->first();
            $user->assignRole($role);
            return $this->sendResponse(new CustomerResource($customer), 201);
        } catch (\Throwable $message) {
            $message = 'Duplicate Email';
            return $this->messageSubscription($message, 403);
        }
    }

    public function getOtp()
    {
        $permitted_chars = '123456789';
        $random_number = substr(str_shuffle($permitted_chars), 0, 4);
        return $random_number;
    }

    public function sendOtp($id)
    {
        $basic = new \Vonage\Client\Credentials\Basic('5a0ffa3c', 'u0a0jmjbs30NP6ps');
        $client = new \Vonage\Client($basic);
        $user = $this->user->customer($id);
        $receiver = ".$this->customer->phone_prefix .$this->customer->phone_number";
        $message = 'Your Loyalty points veritfication code is ' . $this->otp . '';
        $response = $client->sms()->send(new \Vonage\SMS\Message\SMS($receiver, 'LOYALTY', $message));
        $message = $response->current();
        if ($message->getStatus() == 0) {
            return $this->messageSubscription('SMS Sent Successfully', 200);
        } else {
            return $this->messageSubscription('The message failed with status: ' . $message->getStatus() . "\n", 403);
        }
    }

    public function saveOtp($id)
    {
        $user = $this->user->customer($id);
        if ($user['is_verified'] == 0) {
            $otp = $this->getotp();
            $now = date('Y-m-d H:i:s');
            $expirytime = strtotime($now . ' +5 minutes');
            $expiry = date('Y-m-d H:i:s', $expirytime);
            if ($this->checkOtp($id)) {
                $updatedotp = $this->otp->updateotp($user['id'], $otp, $expiry);
                return $this->checkOtp($id);
            } else {
                $newotp = $this->otp->createotp($user['id'], $otp, $expiry);
                return $newotp;
            }
        } else {
            return $this->messageSubscription('Account already verified', 200);
        }
    }

    public function checkOtp($id)
    {
        $otp = $this->otp->otp($id);
        return $otp;
    }

    public function verifyOtp($id, $otp)
    {
        $otp = $this->otp->verifyotp($id, $otp);
        if ($otp) {
            if ($this->expiryCheck($otp->expirytime) == 'expired') {
                $this->deleteotp($otp->id);
                return $this->messageSubscription('OTP has expired', 200);
            }
            $this->user->updatecustomerstatus($otp->user_id);
            $this->deleteotp($otp->id);
            return $this->messageSubscription('You have been successfully verified', 200);
        } else {
            return $this->messageSubscription('Request an OTP', 200);
        }
    }

    public function expiryCheck($expirytime)
    {
        $now = strtotime(date('Y-m-d H:i:s'));
        $expirytime = strtotime($expirytime);
        if ($now > $expirytime) {
            return 'expired';
        }
    }

    public function login(CustomerLoginRequest $request)
    {
        return $this->authCheck($request->email, $request->pin_code);
    }

    public function authCheck($email, $pin_code)
    {
        $user = $this->customer->customerByemail($email);
        if (!$user) {
            return $this->messageSubscription("No user with username $email", 401);
        }
        $user = $this->customer->customerbypincode($pin_code);
        if (!$user) {
            return $this->messageSubscription('Wrong pin code', 401);
        }
        if ($user->is_verified == 0) {
            return $this->messageSubscription('Please verify your account  to continue', 401);
        }
        return $this->createToken($email);
    }

    public function createToken($email)
    {
        $user = $this->user->user_by_username($email);
        $user->token = $user->createToken('anything')->plainTextToken;
        return $this->sendResponse(new LoginResource($user), 201);
    }

    public function getCustomers()
    {
        return CustomerResource::collection($this->customer->customers());
    }

    public function getCustomer($id)
    {
        $customer = $this->customer->customer($id);
        $user = $this->customer->userCustomer($customer->user_id);
        $customer->email = $user->email;
        $customer->phone_number = $user->mobile;
        $customer->phone_prefix = $user->phone_prefix;
        return $this->sendResponse(new SingleCustomerResource($customer), 200);
    }

    public function updateCustomer($request, $id)
    {
        $customer = $this->customer->customer($id);
        $user = $this->user->user($id);
        $customer->update($request->validated());
        $user->update([
            'phone_prefix' => $request->phone_prefix ? $request->phone_prefix : $user->phone_prefix,
            'mobile' => $request->phone_number ? $request->phone_number : $user->phone_number,
            'email' => $request->email ? $request->email : $user->email,
        ]);
        return $this->sendResponse($customer, 201);
    }

    public function destroy($id)
    {
        $this->customer::destroy($id);
        $this->user::destroy($id);
        return $this->messageSubscription('Customer has been deleted', 200);
    }

    public function deleteOtp($id)
    {
        $this->otp::destroy($id);
    }
}
