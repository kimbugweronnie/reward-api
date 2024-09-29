<?php
namespace App\Services;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Program;
use App\Http\Requests\SubscriptionRequest;
use App\Http\Requests\SubscriptionUpdateRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\SubscriptionResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;


class SubscriptionService extends Controller
{
    private $subscription;
    private $user;
    private $program;

    public function __construct(Subscription $subscription,User $user, Program $program) {
        $this->subscription = $subscription;
        $this->user = $user;
        $this->program = $program;
    }

    public function getCardNumber()
    {
        $permitted_chars = '1234567890ABCDEFGHIJKLMNOPQRSTUVXYZ';
        $random_number=substr(str_shuffle($permitted_chars), 0, 19);
        return $random_number;
    }

    public function createSubscription($request)
    {
        $unique_subscription = $this->subscription->uniqueSubscription($request["user_id"],$request['program_id']);
        if($unique_subscription){
            return $this->sendMessageSubscription('Already Subscribed', 208);
        }
        $cardnumber = $this->getCardNumber();
        $merchantid = $this->program->getMerchant($request['program_id']);
        $program = $this->program->program($request['program_id']);
        $user = $this->user->customer($request["user_id"]);
        if($program->status == 0){
            return $this->sendMessageSubscription('The program is inactive', 406);
        }else{
            $subscription = $this->subscription->createsubscription($request,$this->getcardnumber(),$merchantid);
            return $this->sendResponse($subscription, 201);
        }
    }

    public function getSubscriptions($user_id)
    {
        $subscriptions = $this->subscription->userSubscription($user_id);  
        return $this->sendResponse(SubscriptionResource::collection($subscriptions), 200);
    }

    public function getSubscription($id,$user_id)
    {
        $subscription=$this->subscription->subscription($id,$user_id);
        if($subscription){
            return $this->sendResponse(new SubscriptionResource($subscription), 200);
        }else{
            return $this->sendMessageSubscription('Subscription doesnt exist', 204); 
        } 
    }

    public function updateSubscription(SubscriptionUpdateRequest $request,$id,$user_id)
    {
        $subscription=$this->subscription->subscription($id,$user_id); 
        $subscription->update($request->validated());
        return $this->sendResponse($subscription, 201);
    }

    public function unsubscribe($id,$user_id)
    {
        $subscription=$this->subscription->subscription($id,$user_id);
        $subscription->update(['status' => false]);
        return $this->messageSubscription('You have unsubscribed to the program', 200);
    }
    
    public function destroy($id)
    {
        $this->subscription::destroy($id);
        return $this->messageSubscription("Subscription has been deleted", 200);  
    }

}