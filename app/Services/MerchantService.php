<?php
namespace App\Services;
use App\Models\Merchant;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Program;
use App\Models\Point;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Requests\MerchantRequest;
use App\Http\Requests\MerchantUpdateRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\MerchantResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class MerchantService extends Controller
{
    private $merchant;
    private $subscription;
    private $program;
    private $point;
    private $user;

    public function __construct(Merchant $merchant, User $user, Subscription $subscription, Program $program, Point $point)
    {
        $this->merchant = $merchant;
        $this->user = $user;
        $this->subscription = $subscription;
        $this->program = $program;
        $this->point = $point;
    }

    /**
     * Create a merchant and assign role to user.
     */
    public function createMerchant($request):object
    {
        try {
            $user = $this->createUserForMerchant($request);
            $merchant = $this->createMerchantRecord($request, $user);
            $this->assignRoleToUser($user, $request['user_type']);
            return $this->sendResponse(new MerchantResource($merchant), 201);
        } catch (\Throwable $e) {
            return $this->handleDuplicateEmailError();
        }
    }

    /**
     * Get a list of all merchants.
     */
    public function getMerchants():object
    {
        return $this->sendGetResponse(MerchantResource::collection($this->merchant->all()), 200);
    }

    /**
     * Get programs for a given merchant.
     */
    public function getPrograms($id):object
    {
        return $this->sendGetResponse(count($this->getMerchantPrograms($id)), 200);
    }

    /**
     * Get a single merchant by id.
     */
    public function getMerchant($id):object
    {
        return $this->sendGetResponse($this->findMerchantById($id), 200);
    }

    /**
     * Get unique subscribers of a merchant.
     */
    public function getSubscribers($id):object
    {
        return $this->sendGetResponse(count($this->getUniqueSubscribers($id)), 200);
    }

    /**
     * Get active programs for a merchant.
     */
    public function activePrograms($merchant_id):object
    {
        return $this->sendGetResponse(count($this->getActivePrograms($merchant_id)), 200);
    }

    /**
     * Get inactive programs for a merchant.
     */
    public function inactivePrograms($merchant_id):object
    {
        return $this->sendGetResponse(count($this->getInactivePrograms($merchant_id)), 200);
    }

    /**
     * Get points associated with a merchant.
     */
    public function getPoints($merchant_id)
    {
        return $this->sendGetResponse(intval($this->getMerchantPoints($merchant_id)), 200);
    }

    /**
     * Get unredeemed points for a merchant.
     */
    public function unRedeemedPoints($merchant_id):object
    {
        $totalPoints = $this->getMerchantPoints($merchant_id);
        $redeemedPoints = $this->getRedeemedPoints($merchant_id);
        $unredeemedPoints = $this->calculateUnredeemedPoints($totalPoints, $redeemedPoints);
        return $this->sendGetResponse($unredeemedPoints, 200);
    }

    /**
     * Get redeemed points for a merchant.
     */
    public function pointsRedeemed($merchant_id):object
    {
        return $this->sendGetResponse(intval($this->getRedeemedPoints($merchant_id)), 200);
    }

    /**
     * Get expired points for a merchant.
     */
    public function expiredPoints($merchant_id):object
    {
        return $this->sendGetResponse(intval($this->getExpiredPoints($merchant_id)), 200);
    }

    /**
     * Update a merchant's details.
     */
    public function updateMerchant($request, $id):object
    {
        $merchant = $this->findMerchantById($id);
        $merchant->update($request->validated());
        return $this->sendResponse($merchant, 201);
    }

    /**
     * Delete a merchant and its user.
     */
    public function destroy($id):object
    {
        $this->deleteMerchantAndUser($id);
        return $this->messageSubscription('Merchant has been deleted', 200);
    }

 
    private function createUserForMerchant($request):object
    {
        return $this->user->createUser($request['email'], $request['phone_number'], $request['phone_prefix'], $request['password']);
    }

    private function createMerchantRecord($request, $user):object
    {
        return $this->merchant->createMerchant(
            $request['merchant_name'],
            $request['merchant_description'],
            $request['location'],
            $request['user_type'],
            $user->id
        );
    }

    private function assignRoleToUser($user, $roleName)
    {
        $role = Role::where('name', $roleName)->first();
        $user->assignRole($role);
    }

    private function handleDuplicateEmailError()
    {
        return $this->messageSubscription('Duplicate Email', 403);
    }

    private function getMerchantPrograms($id)
    {
        return $this->program->merchantPrograms($id);
    }

    private function findMerchantById($id)
    {
        return $this->merchant->merchant($id);
    }

    private function getUniqueSubscribers($merchant_id)
    {
        $subscribers = $this->subscription->subscribers($merchant_id);
        $uniqueSubscribers = array_unique(array_column($subscribers->toArray(), 'user_id'));
        return $uniqueSubscribers;
    }

    private function getActivePrograms($merchant_id)
    {
        return $this->program->activePrograms($merchant_id);
    }

    private function getInactivePrograms($merchant_id)
    {
        return $this->program->inactivePrograms($merchant_id);
    }

    private function getMerchantPoints($merchant_id)
    {
        return $this->program->getPoints($merchant_id);
    }

    private function getRedeemedPoints($merchant_id)
    {
        return $this->point->pointsRedeemed($merchant_id);
    }

    private function calculateUnredeemedPoints($totalPoints, $redeemedPoints):int
    {
        return $totalPoints - $redeemedPoints;
    }

    private function getExpiredPoints($merchant_id)
    {
        return $this->program->expiredPoints($merchant_id);
    }

    private function deleteMerchantAndUser($id):void
    {
        $this->merchant::destroy($id);
        $this->user::destroy($id);
    }
}
