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


class TransactionService extends Controller
{
    private $transaction;
    private $subscription;
    private $point;
    private $program;
    private $merchant;
    private $user;

    public function __construct(Transaction $transaction, Subscription $subscription,Point $point,Program $program,Merchant $merchant,User $user) {
        $this->transaction = $transaction;
        $this->subscription = $subscription;
        $this->point = $point;
        $this->program = $program;
        $this->merchant = $merchant;
        $this->user = $user;
    }

    public function earnPoints($request)
    {
        $subscription = $this->subscription->subscription($request['subscription_id'],$request['user_id']);
        if($subscription){
            $program = $this->program->program($request['program_id']);
            if($program->status == 0){
                return $this->sendResponse('The program is inactive', 200);
            }else{
                $user = $this->user->user($request['user_id']); 
                $createdpoints = $this->point->earnPoints($request);
                $balance = $user->points + intval($request['points_awarded']);
                $this->user->updatePoints($request['user_id'],$balance);
                return $this->sendResponse($createdpoints, 201);
            }  
        }else{
            return $this->sendMessage("Please Subscribe", 200);
        }
    }

    public function spendPoints($request)
    {
        $program = $this->program->program($request['program_id']);
        if($program->status == 0){
            return $this->sendResponse('The program is inactive', 200);
        }else{
            $user = $this->user->user($request['user_id']); 
            $createdpoints = $this->point->earnPoints($request);
            $balance = $user->points - intval($request['points_used']);
            $this->user->updatePoints($request['user_id'],$balance);
            return $this->sendResponse($createdpoints, 201);
        }  
    }

    public function getPoints($id)
    {
        $merchants = [];
        $usermerchants = $this->getMerchants($id);
        foreach ($usermerchants as $usermerchant) {
            $pointsEarned = $this->point->pointsMerchants($id,$usermerchant->id);
            $pointsSpent = $this->point->pointsSpentMerchant($id,$usermerchant->id);
            $balance = $pointsEarned - $pointsSpent;
            array_push($merchants,['merchant_id' => $usermerchant->id, 'merchant_name' => $usermerchant->merchant_name,'points' => intval($balance)]);
        }
        return $this->sendResponse($merchants, 200);
    }

    public function getMerchants($id)
    {
        $merchantids = [];
        $usermerchants = [];
        $subscriptionprograms = $this->subscription->userSubscriptions($id);
        foreach ($subscriptionprograms as $program) {
            $userprogram = $this->program->program($program['program_id']);
            array_push($merchantids,$userprogram->merchant_id);
        }
        $merchants = array_unique($merchantids);
        foreach ($merchants as $merchant) {
            array_push($usermerchants,$this->merchant->merchant($merchant));
        } 
        return $usermerchants;   
    }

    public function getPointsEarned($id)
    {
        $merchants = [];
        $usermerchants = $this->getMerchants($id);
        foreach ($usermerchants as $usermerchant) {
            $pointsEarned = $this->point->pointsMerchants($id,$usermerchant->id);
            array_push($merchants,['merchant_id' => $usermerchant->id, 'merchant_name' => $usermerchant->merchant_name,'points' => intval($pointsEarned)]);
        }
        return $this->sendResponse($merchants, 200);
    }

    public function getPointsSpent($id)
    {
        $merchants = [];
        $usermerchants = $this->getMerchants($id);
        foreach ($usermerchants as $usermerchant) {
            $pointsSpent = $this->point->pointsSpentMerchant($id,$usermerchant->id);
            array_push($merchants,['merchant_id' => $usermerchant->id, 'merchant_name' => $usermerchant->merchant_name,'points' => intval($pointsSpent)]);
        }
        return $this->sendResponse($merchants, 200);
    }

    public function getTransactions()
    {
        $transactions = $this->transaction->all();  
        return $this->sendResponse($transactions, 200);
    }

    public function getTransaction($id)
    {
        $transaction=$this->transaction->transaction($id);
        return $this->sendResponse($transaction, 200);
    }

    public function destroy($id)
    {
        $this->transaction::destroy($id);
        return $this->sendResponse("Transaction has been deleted", 200);  
    }

}