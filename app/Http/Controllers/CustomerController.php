<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Services\CustomerService;

class CustomerController extends Controller
{
    public $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(): object
    {
        return $this->customerService->getCustomers();
    }

    public function store(CustomerRequest $request):object
    {
        return $this->customerService->store($request->validated());
    }

    public function getOtp(Request $request):object
    {
        return $this->customerService->saveotp($request->id);
    }

    public function verifyOtp(Request $request):object
    {
        return $this->customerService->verifyotp($request->id, $request->otp);
    }

    public function show($id):object
    {
        return $this->customerService->getcustomer($id);
    }

    public function update(CustomerUpdateRequest $request, $id):object
    {
        return $this->customerService->updateCustomer($request, $id);
    }

    public function destroy($id):object
    {
        return $this->customerService->destroy($id);
    }
}
