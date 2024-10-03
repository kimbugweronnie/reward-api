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

    public function index(): object
    {
        return $this->userservice->getcustomers();
    }

    public function login(LoginRequest $request): object
    {
        return $this->userservice->login($request);
    }

    public function store(UserRequest $request): object
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

    public function update(UserUpdateRequest $request, $id): object
    {
        return $this->userservice->updatecustomer($request, $id);
    }

    public function destroy($id): object
    {
        return $this->userservice->destroy($id);
    }
}
