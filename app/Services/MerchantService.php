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

    public function createMerchant($request)
    {
        try {
            $user = $this->user->createUser($request['email'], $request['phone_number'], $request['phone_prefix'], $request['password']);
            $merchant = $this->merchant->createMerchant($request['merchant_name'], $request['merchant_description'],$request['location'],$request['user_type'], $user->id);
            $role1 = Role::where('name', $request['user_type'])->first();
            $user->assignRole($role1);
            return $this->sendResponse(new MerchantResource($merchant), 201);
        } catch (\Throwable $message) {
            $message = 'Duplicate Email';
            return $this->messageSubscription($message, 403);
        }
    }

    public function getMerchants()
    {
        $merchants = MerchantResource::collection($this->merchant->all());
        return $this->sendResponse($merchants, 200);
    }

    public function getPrograms($id)
    {
        $programs = $this->program->merchantPrograms($id);
        return $this->sendResponse(count($programs), 200);
    }

    public function getMerchant($id)
    {
        $merchant = $this->merchant->merchant($id);
        return $this->sendResponse($merchant, 200);
    }

    public function getSubscribers($id)
    {
        $uniquesubscribers = [];
        $subscribers = $this->subscription->subscribers($id);
        foreach ($subscribers as $subscriber) {
            array_push($uniquesubscribers, $subscriber->user_id);
        }
        $subscribers = array_unique($uniquesubscribers);
        return $this->sendResponse(count($subscribers), 200);
    }

    public function activePrograms($merchant_id)
    {
        $programs = $this->program->activePrograms($merchant_id);
        return $this->sendResponse(count($programs), 200);
    }

    public function inactivePrograms($merchant_id)
    {
        $programs = $this->program->inactivePrograms($merchant_id);
        return $this->sendResponse(count($programs), 200);
    }

    public function getPoints($merchant_id)
    {
        $points = $this->program->getPoints($merchant_id);
        return $this->sendResponse(intval($points), 200);
    }

    public function unRedeemedPoints($merchant_id)
    {
        $points = $this->program->getPoints($merchant_id);
        $earnedpoints = $this->point->pointsRedeemed($merchant_id);
        $unredeemedpoints = $points - $earnedpoints;
        return $this->sendResponse($unredeemedpoints, 200);
    }

    public function pointsRedeemed($merchant_id)
    {
        $points = $this->point->pointsRedeemed($merchant_id);
        return $this->sendResponse(intval($points), 200);
    }

    public function expiredPoints($merchant_id)
    {
        $points = $this->program->expiredPoints($merchant_id);
        return $this->sendResponse(intval($points), 200);
    }

    public function updateMerchant($request, $id)
    {
        $merchant = $this->merchant->merchant($id);
        $user = $this->user->user($id);
        $merchant->update($request->validated());
        $user->update([
            'phone_prefix' => $request->phone_prefix ? $request->phone_prefix : $user->phone_prefix,
            'mobile' => $request->phone_number ? $request->phone_number : $user->phone_number,
            'email' => $request->email ? $request->email : $user->email,
        ]);
        return $this->sendResponse($merchant, 201);
    }

    public function destroy($id)
    {
        $this->merchant::destroy($id);
        $this->user::destroy($id);
        return $this->messageSubscription('Merchant has been deleted', 200);
    }
}
