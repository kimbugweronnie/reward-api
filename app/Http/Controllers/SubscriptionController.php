<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Requests\SubscriptionRequest;
use App\Http\Requests\SubscriptionUpdateRequest;
use App\Services\SubscriptionService;

class SubscriptionController extends Controller
{
    private $subscriptionService;
    
    public function __construct(SubscriptionService $subscriptionService) {
        $this->subscriptionService = $subscriptionService; 
    }
   
    public function index($user_id):object
    {
        return $this->subscriptionService->getSubscriptions($user_id);
    }

    public function store(SubscriptionRequest $request):object
    {
        return $this->subscriptionService->createSubscription($request->validated());
    }

    public function show($id,$user_id):object
    {
        return $this->subscriptionService->getSubscription($id,$user_id);
    }

    public function update(SubscriptionUpdateRequest $request, $id,$user_id):object
    {
        return $this->subscriptionService->updateSubscription($request,$id,$user_id);
    }

    public function unsubscribe($id,$user_id):object
    {
        return $this->subscriptionService->unsubscribe($id,$user_id);
    }

    public function destroy($id):object
    {
        return $this->subscriptionService->destroy($id);
    }
}
