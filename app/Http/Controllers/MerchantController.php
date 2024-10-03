<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Requests\MerchantRequest;
use App\Http\Requests\MerchantUpdateRequest;
use App\Services\MerchantService;

class MerchantController extends Controller
{
    private $merchantService;
    public function __construct(MerchantService $merchantService) {
      
        $this->merchantService = $merchantService; 
    }
   
    public function index()
    {
        return $this->merchantService->getMerchants();
    }

    public function subscribers($id)
    {
        return $this->merchantService->getSubscribers($id);
    }

    public function programs($id)
    {
        return $this->merchantService->getPrograms($id);
    }

    public function activePrograms($id)
    {
        return $this->merchantService->activePrograms($id);
    }

    public function inactivePrograms($id)
    {
        return $this->merchantService->inactivePrograms($id);
    }

    public function getPoints($id)
    {
        return $this->merchantService->getPoints($id);
    }

    public function unRedeemedPoints($id)
    {
        return $this->merchantService->unRedeemedPoints($id);
    }

    public function pointsRedeemed($id)
    {
        return $this->merchantService->pointsRedeemed($id); 
    }

    public function expiredPoints($id)
    {
        return $this->merchantService->expiredPoints($id); 
    }
    
    public function store(MerchantRequest $request)
    {
        return $this->merchantService->createMerchant($request->validated());
    }

    public function show($id)
    {
        return $this->merchantService->getMerchant($id);
    }

    public function update(MerchantUpdateRequest $request, $id):object
    {
        return $this->merchantService->updateMerchant($request,$id);
    }

    public function destroy($id):object
    {
        return $this->merchantService->destroy($id);
    }
}
