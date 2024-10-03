<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Requests\TransactionRequest;
use App\Http\Requests\SpendTransactionRequest;
use App\Services\TransactionService;

class TransactionController extends Controller
{
    private $transactionService;

    public function __construct(TransactionService $transactionService) {
        $this->transactionService = $transactionService; 
    }
   
    public function index():object
    {
        return $this->transactionService->getTransactions();
    }

    public function earn(TransactionRequest $request):object
    {
        return $this->transactionService->earnPoints($request->validated());
    }

    public function show($id):object
    {
        return $this->transactionService->getTransaction($id);
    }

    public function spend(SpendTransactionRequest $request):object
    {
        return $this->transactionService->spendPoints($request->validated());
    }

    public function getPoints($id):object
    {
        return $this->transactionService->getPoints($id);
    }

    public function getPointsEarned($id):object
    {
        return $this->transactionService->getPointsEarned($id);
    }

    public function getPointsSpent($id):object
    {
        return $this->transactionService->getPointsSpent($id);
    }

    public function destroy($id):object
    {
        return $this->transactionService->destroy($id);
    }
}
