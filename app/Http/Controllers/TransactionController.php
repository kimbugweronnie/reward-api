<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Requests\TransactionRequest;
use App\Http\Requests\SpendTransactionRequest;
use App\Services\TransactionService;

class TransactionController extends Controller
{
    public function __construct(TransactionService $transactionService) {
        $this->transactionService = $transactionService; 
    }
   
    public function index()
    {
        return $this->transactionService->getTransactions();
    }

    public function earn(TransactionRequest $request)
    {
        return $this->transactionService->earnPoints($request->validated());
    }

    public function show($id)
    {
        return $this->transactionService->getTransaction($id);
    }

    public function spend(SpendTransactionRequest $request)
    {
        return $this->transactionService->spendPoints($request->validated());
    }

    public function getPoints($id)
    {
        return $this->transactionService->getPoints($id);
    }

    public function getPointsEarned($id)
    {
        return $this->transactionService->getPointsEarned($id);
    }

    public function getPointsSpent($id)
    {
        return $this->transactionService->getPointsSpent($id);
    }

    public function destroy($id)
    {
        return $this->transactionService->destroy($id);
    }
}
