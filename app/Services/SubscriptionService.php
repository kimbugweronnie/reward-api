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

    public function __construct(Subscription $subscription, User $user, Program $program)
    {
        $this->subscription = $subscription;
        $this->user = $user;
        $this->program = $program;
    }

   

    public function createSubscription($request):object
    {
        if ($this->isAlreadySubscribed($request["user_id"], $request['program_id'])) {
            return $this->sendMessageSubscription('Already Subscribed', 208);
        }

        $cardNumber = $this->generateCardNumber();
        $merchantId = $this->getMerchantIdByProgram($request['program_id']);
        $program = $this->getProgram($request['program_id']);

        if ($this->isProgramInactive($program)) {
            return $this->sendMessageSubscription('The program is inactive', 406);
        }

        $subscription = $this->createSubscriptionRecord($request, $cardNumber, $merchantId);
        return $this->sendResponse($subscription, 201);
    }

    public function getSubscriptions($user_id):object
    {
        $subscriptions = $this->getUserSubscriptions($user_id);
        return $this->sendGetResponse(SubscriptionResource::collection($subscriptions), 200);
    }

    public function getSubscription($id, $user_id):object
    {
        $subscription = $this->findSubscription($id, $user_id);
        if ($subscription) {
            return $this->sendResponse(new SubscriptionResource($subscription), 200);
        }
        return $this->sendMessageSubscription('Subscription doesn\'t exist', 204);
    }

    public function updateSubscription(SubscriptionUpdateRequest $request, $id, $user_id):object
    {
        $subscription = $this->findSubscription($id, $user_id);
        $this->updateSubscriptionRecord($subscription, $request);
        return $this->sendResponse($subscription, 201);
    }

    public function unsubscribe($id, $user_id):object
    {
        $subscription = $this->findSubscription($id, $user_id);
        $this->deactivateSubscription($subscription);
        return $this->messageSubscription('You have unsubscribed from the program', 200);
    }

    public function destroy($id):object
    {
        $this->deleteSubscription($id);
        return $this->messageSubscription("Subscription has been deleted", 200);
    }

   
    /**
     * Check if the user is already subscribed to the program
     */
    private function isAlreadySubscribed($user_id, $program_id):object
    {
        return $this->subscription->uniqueSubscription($user_id, $program_id);
    }

    /**
     * Generate a random card number
     */
    private function generateCardNumber():int
    {
        $permittedChars = '1234567890ABCDEFGHIJKLMNOPQRSTUVXYZ';
        return substr(str_shuffle($permittedChars), 0, 19);
    }

    /**
     * Get merchant ID associated with a program
     */
    private function getMerchantIdByProgram($program_id):object
    {
        return $this->program->getMerchant($program_id);
    }

    /**
     * Fetch a program by ID
     */
    private function getProgram($program_id):object
    {
        return $this->program->program($program_id);
    }

    /**
     * Check if the program is inactive
     */
    private function isProgramInactive($program):int
    {
        return $program->status == 0;
    }

    /**
     * Create a new subscription record
     */
    private function createSubscriptionRecord($request, $cardNumber, $merchantId):object
    {
        return $this->subscription->createsubscription($request, $cardNumber, $merchantId);
    }

    /**
     * Get subscriptions for a specific user
     */
    private function getUserSubscriptions($user_id):object
    {
        return $this->subscription->userSubscription($user_id);
    }

    /**
     * Find a subscription by ID and user ID
     */
    private function findSubscription($id, $user_id):object
    {
        return $this->subscription->subscription($id, $user_id);
    }

    /**
     * Update an existing subscription
     */
    private function updateSubscriptionRecord($subscription, $request):void
    {
        $subscription->update($request->validated());
    }

    /**
     * Deactivate a subscription (unsubscribe)
     */
    private function deactivateSubscription($subscription):void
    {
        $subscription->update(['status' => false]);
    }

    /**
     * Delete a subscription by ID
     */
    private function deleteSubscription($id):void
    {
        $this->subscription::destroy($id);
    }
}
