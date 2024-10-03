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

    public function store($request):object
    {
        try {
            $user = $this->createUser($request);
            $customer = $this->createCustomer($request, $user);
            $this->assignRoleToUser($user, $request['user_type']);

            return $this->sendResponse(new CustomerResource($customer), 201);
        } catch (\Throwable $e) {
            return $this->handleDuplicateEmail($e);
        }
    }

    private function createUser($request):object
    {
        return $this->user->createUser($request['email'], $request['phone_number'], $request['phone_prefix'], $request['password']);
    }

    private function createCustomer($request, $user):object
    {
        return $this->customer->createCustomer($request['first_name'], $request['last_name'], $request['user_type'], $user->id);
    }

    private function assignRoleToUser($user, $roleName):void
    {
        $role = Role::where('name', $roleName)->first();
        $user->assignRole($role);
    }

    private function handleDuplicateEmail($e):object
    {
        return $this->messageSubscription('Duplicate Email', 403);
    }

    // OTP Methods
    public function getOtp()
    {
        return $this->generateOtp();
    }

    private function generateOtp():int
    {
        $permittedChars = '123456789';
        return substr(str_shuffle($permittedChars), 0, 4);
    }

    public function sendOtp($id):object
    {
        $user = $this->user->customer($id);
        $receiver = $this->formatReceiver($user);
        $message = $this->composeOtpMessage($this->otp);

        return $this->sendSms($receiver, $message);
    }

    private function formatReceiver($user):string
    {
        return "{$user->phone_prefix}{$user->phone_number}";
    }

    private function composeOtpMessage($otp):string
    {
        return 'Your Loyalty points verification code is ' . $otp;
    }

    private function sendSms($receiver, $message):object
    {
        $client = new \Vonage\Client(new \Vonage\Client\Credentials\Basic('API_KEY', 'API_SECRET'));
        $response = $client->sms()->send(new \Vonage\SMS\Message\SMS($receiver, 'LOYALTY', $message));

        return $this->handleSmsResponse($response);
    }

    private function handleSmsResponse($response):object
    {
        $message = $response->current();
        if ($message->getStatus() == 0) {
            return $this->messageSubscription('SMS Sent Successfully', 200);
        } else {
            return $this->messageSubscription('SMS failed with status: ' . $message->getStatus(), 403);
        }
    }

    // OTP Verification and Expiry
    public function verifyOtp($id, $otp):object
    {
        $otpRecord = $this->otp->verifyotp($id, $otp);

        if ($otpRecord) {
            return $this->handleOtpVerification($otpRecord);
        }

        return $this->messageSubscription('Request an OTP', 200);
    }

    private function handleOtpVerification($otpRecord):object
    {
        if ($this->isOtpExpired($otpRecord->expirytime)) {
            $this->deleteOtp($otpRecord->id);
            return $this->messageSubscription('OTP has expired', 200);
        }

        $this->user->updatecustomerstatus($otpRecord->user_id);
        $this->deleteOtp($otpRecord->id);

        return $this->messageSubscription('You have been successfully verified', 200);
    }

    private function isOtpExpired($expiryTime):bool
    {
        return strtotime(date('Y-m-d H:i:s')) > strtotime($expiryTime);
    }

    // Generic Helper Methods
    public function deleteOtp($id):void
    {
        $this->otp::destroy($id);
    }

    // Auth Methods
    public function login(CustomerLoginRequest $request):object
    {
        return $this->authCheck($request->email, $request->pin_code);
    }

    private function authCheck($email, $pinCode):object
    {
        $user = $this->getCustomerByEmail($email);
        if (!$user) {
            return $this->messageSubscription("No user with email $email", 401);
        }
        return $this->checkPinCode($user, $pinCode);
    }

    private function getCustomerByEmail($email):object
    {
        return $this->customer->customerByemail($email);
    }

    private function checkPinCode($user, $pinCode):object
    {
        $user = $this->customer->customerbypincode($pinCode);

        if (!$user) {
            return $this->messageSubscription('Wrong pin code', 401);
        }

        if (!$this->isVerified($user)) {
            return $this->messageSubscription('Please verify your account to continue', 401);
        }

        return $this->createToken($user->email);
    }

    private function isVerified($user):bool
    {
        return $user->is_verified != 0;
    }

    private function createToken($email):object
    {
        $user = $this->user->user_by_username($email);
        $user->token = $user->createToken('anything')->plainTextToken;

        return $this->sendResponse(new LoginResource($user), 201);
    }

    // Customer management
    public function getCustomers():object
    {
        return CustomerResource::collection($this->customer->customers());
    }

    public function getCustomer($id):object
    {
        $customer = $this->customer->customer($id);
        $user = $this->customer->userCustomer($customer->user_id);

        $customer->email = $user->email;
        $customer->phone_number = $user->mobile;
        $customer->phone_prefix = $user->phone_prefix;

        return $this->sendResponse(new SingleCustomerResource($customer), 200);
    }

    public function updateCustomer($request, $id):object
    {
        $customer = $this->customer->customer($id);
        $user = $this->user->user($id);

        $this->updateCustomerData($customer, $request);
        $this->updateUserData($user, $request);

        return $this->sendResponse($customer, 201);
    }

    private function updateCustomerData($customer, $request):void
    {
        $customer->update($request->validated());
    }

    private function updateUserData($user, $request):void
    {
        $user->update([
            'phone_prefix' => $request->phone_prefix ?? $user->phone_prefix,
            'mobile' => $request->phone_number ?? $user->phone_number,
            'email' => $request->email ?? $user->email,
        ]);
    }

    public function destroy($id):object
    {
        $this->customer::destroy($id);
        $this->user::destroy($id);

        return $this->messageSubscription('Customer has been deleted', 200);
    }
}
