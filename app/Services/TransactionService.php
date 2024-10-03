<?php
namespace App\Services;
use App\Models\Transaction;
use App\Models\Subscription;
use App\Models\Point;
use App\Models\Program;
use App\Models\Merchant;
use App\Models\User;
use App\Http\Requests\TransactionRequest;
use App\Http\Requests\SpendTransactionRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\TransactionResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

namespace App\Services;

use App\Models\Transaction;
use App\Models\Subscription;
use App\Models\Point;
use App\Models\Program;
use App\Models\Merchant;
use App\Models\User;
use App\Http\Requests\TransactionRequest;
use App\Http\Requests\SpendTransactionRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\TransactionResource;
use App\Http\Controllers\Controller;

class TransactionService extends Controller
{
    private $transaction;
    private $subscription;
    private $point;
    private $program;
    private $merchant;
    private $user;

    public function __construct(Transaction $transaction, Subscription $subscription, Point $point, Program $program, Merchant $merchant, User $user)
    {
        $this->transaction = $transaction;
        $this->subscription = $subscription;
        $this->point = $point;
        $this->program = $program;
        $this->merchant = $merchant;
        $this->user = $user;
    }

   

    public function earnPoints($request):object
    {
        $subscription = $this->getSubscription($request);
        $program = $this->getUserProgram($request['program_id']);

        if ($this->isProgramInactive($program)) {
            return $this->sendResponse('The program is inactive', 200);
        }

        if ($subscription) {
            return $this->createAndSendPoints($request);
        }

        return $this->sendMessage('Please Subscribe', 200);
    }

    public function spendPoints($request):object
    {
        $program = $this->getUserProgram($request['program_id']);
        if ($this->isProgramInactive($program)) {
            return $this->sendResponse('The program is inactive', 200);
        }

        return $this->createAndSendPoints($request);
    }

    public function updatePoints($request):void
    {
        $user = $this->getUser($request['user_id']);
        $balance = $this->calculateNewPointsBalance($user, $request['points_used']);
        $this->updateUserPoints($request['user_id'], $balance);
    }

    public function getPoints($id):object
    {
        $merchants = $this->getUserMerchantsPoints($id);
        return $this->sendResponse($merchants, 200);
    }

    public function getPointsEarned($id):object
    {
        $merchants = $this->getUserMerchantsPointsEarned($id);
        return $this->sendResponse($merchants, 200);
    }

    public function getPointsSpent($id):object
    {
        $merchants = $this->getUserMerchantsPointsSpent($id);
        return $this->sendResponse($merchants, 200);
    }

    public function getTransactions():object
    {
        $transactions = $this->getAllTransactions();
        return $this->sendResponse($transactions, 200);
    }

    public function getTransaction($id):object
    {
        $transaction = $this->findTransaction($id);
        return $this->sendResponse($transaction, 200);
    }

    public function destroy($id):object
    {
        $this->deleteTransaction($id);
        return $this->sendResponse('Transaction has been deleted', 200);
    }

   
    private function getSubscription($request):object
    {
        return $this->subscription->subscription($request['subscription_id'], $request['user_id']);
    }

    private function getUserProgram($program_id):object
    {
        return $this->program->program($program_id);
    }

    private function isProgramInactive($program):bool
    {
        return $program->status == 0;
    }

    private function createAndSendPoints($request):object
    {
        $createdPoints = $this->point->earnPoints($request);
        return $this->sendResponse($createdPoints, 201);
    }

    private function getUser($user_id):object
    {
        return $this->user->user($user_id);
    }

    private function calculateNewPointsBalance($user, $pointsUsed):int
    {
        return $user->points - intval($pointsUsed);
    }

    private function updateUserPoints($user_id, $balance):void
    {
        $this->user->updatePoints($user_id, $balance);
    }

    private function getAllTransactions():object
    {
        return $this->transaction->all();
    }

    private function findTransaction($id):object
    {
        return $this->transaction->transaction($id);
    }

    private function deleteTransaction($id):void
    {
        $this->transaction::destroy($id);
    }

    private function getUserMerchantsPoints($user_id):array
    {
        $userMerchants = $this->getMerchants($user_id);
        $merchants = [];

        foreach ($userMerchants as $merchant) {
            $pointsEarned = $this->point->pointsMerchants($user_id, $merchant->id);
            $pointsSpent = $this->point->pointsSpentMerchant($user_id, $merchant->id);
            $balance = $pointsEarned - $pointsSpent;

            $merchants[] = [
                'merchant_id' => $merchant->id,
                'merchant_name' => $merchant->merchant_name,
                'points' => intval($balance),
            ];
        }

        return $merchants;
    }

    private function getUserMerchantsPointsEarned($user_id):array
    {
        $userMerchants = $this->getMerchants($user_id);
        $merchants = [];

        foreach ($userMerchants as $merchant) {
            $pointsEarned = $this->point->pointsMerchants($user_id, $merchant->id);
            $merchants[] = [
                'merchant_id' => $merchant->id,
                'merchant_name' => $merchant->merchant_name,
                'points' => intval($pointsEarned),
            ];
        }

        return $merchants;
    }

    private function getUserMerchantsPointsSpent($user_id):array
    {
        $userMerchants = $this->getMerchants($user_id);
        $merchants = [];

        foreach ($userMerchants as $merchant) {
            $pointsSpent = $this->point->pointsSpentMerchant($user_id, $merchant->id);
            $merchants[] = [
                'merchant_id' => $merchant->id,
                'merchant_name' => $merchant->merchant_name,
                'points' => intval($pointsSpent),
            ];
        }

        return $merchants;
    }

    private function getMerchants($user_id):array
    {
        $merchantIds = [];
        $userMerchants = [];

        $subscriptionPrograms = $this->subscription->userSubscriptions($user_id);
        foreach ($subscriptionPrograms as $program) {
            $userProgram = $this->program->program($program['program_id']);
            $merchantIds[] = $userProgram->merchant_id;
        }

        $merchantIds = array_unique($merchantIds);
        foreach ($merchantIds as $merchantId) {
            $userMerchants[] = $this->merchant->merchant($merchantId);
        }

        return $userMerchants;
    }
}
