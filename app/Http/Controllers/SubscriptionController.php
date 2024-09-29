<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Requests\SubscriptionRequest;
use App\Http\Requests\SubscriptionUpdateRequest;
use App\Services\SubscriptionService;

class SubscriptionController extends Controller
{
    public function __construct(SubscriptionService $subscriptionService) {
        $this->subscriptionService = $subscriptionService; 
    }
   
    public function index($user_id)
    {
        return $this->subscriptionService->getSubscriptions($user_id);
    }

    public function store(SubscriptionRequest $request)
    {
        return $this->subscriptionService->createSubscription($request->validated());
    }

    public function show($id,$user_id)
    {
        return $this->subscriptionService->getSubscription($id,$user_id);
    }

    public function update(SubscriptionUpdateRequest $request, $id,$user_id)
    {
        return $this->subscriptionService->updateSubscription($request,$id,$user_id);
    }

    public function unsubscribe($id,$user_id)
    {
        return $this->subscriptionService->unsubscribe($id,$user_id);
    }

    public function destroy($id)
    {
        return $this->subscriptionService->destroy($id);
    }
}
