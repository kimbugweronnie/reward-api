<?php
namespace App\Services;
use App\Models\PasswordReset;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Controllers\Controller;
use App\Mail\PasswordResetEmail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Notifications\EmailNotification;


class PasswordResetService extends Controller
{
    private $passwordreset;
    private $user;
 
    public function __construct(PasswordReset $passwordreset, User $user) {
        $this->passwordreset = $passwordreset;
        $this->user = $user;
    }

    public function submitforgetpassword($email):object
    {
        $passwordreset = $this->passwordreset->create_record($email); 
        $user = $this->user->user_by_email($email);
        $url = url($passwordreset->token);
        Mail::to($email)->send(new PasswordResetEmail($url));
        return $this->send_response("Email has been sent ", 201);  
    }

    public function passwordreset($email,$password):object
    {
        $this->user::where('email','=',$email)->update(['password' =>$password]);
        $this->passwordreset->delete_by_email($email);
        return $this->send_response("Password Updated", 201);  
    }
}

