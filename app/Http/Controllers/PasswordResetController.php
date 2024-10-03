<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\PasswordRequest;
use App\Services\PasswordResetService;

class PasswordResetController extends Controller
{
    public $passwordresetservice;

    public function __construct(PasswordResetService $passwordresetservice) {
        $this->passwordresetservice = $passwordresetservice; 
    }

    public function forgotpassword(ForgotPasswordRequest $request):object
    {
        return $this->passwordresetservice->submitforgetpassword($request->validated());
    }

    public function passwordreset(PasswordRequest $request):object
    {
        return $this->passwordresetservice->passwordreset($request->email,$request->password);
    }
}
