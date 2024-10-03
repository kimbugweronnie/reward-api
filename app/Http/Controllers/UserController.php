<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Services\UserService;

class UserController extends Controller
{
    public $userservice;

    public function __construct(UserService $userservice)
    {
        $this->userservice = $userservice;
    }

    public function index()
    {
        return $this->userservice->getcustomers();
    }

    public function login(LoginRequest $request)
    {
        return $this->userservice->login($request);
    }

    public function store(UserRequest $request)
    {
        return $this->userservice->registration($request->validated());
    }

    public function show($id): object
    {
        return $this->userservice->getcustomer($id);
    }

    public function edit($id)
    {
    }

    public function update(UserUpdateRequest $request, $id)
    {
        return $this->userservice->updatecustomer($request, $id);
    }

    public function destroy($id)
    {
        return $this->userservice->destroy($id);
    }
}
