<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\CompanyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/v2/login',[UserController::class,'login']);
Route::post('/v2/customer-register',[CustomerController::class,'store']);
Route::post('/v2/merchant-register',[MerchantController::class,'store']);
// Route::post('/v2/otp',[CustomerController::class,'getotp']);
// Route::post('/v2/otp/verify',[CustomerController::class,'verifyotp']);
Route::post('/v2/request-demo',[CompanyController::class,'store']);

// Route::post('/v2/forgot-password',[PasswordResetController::class,'forgotpassword']);
// Route::post('/v2/passward-reset',[PasswordResetController::class,'passwordreset']);

Route::group(['middleware' => ['auth:sanctum','role:merchant']], function () {
   
    Route::get('/v2/{id}/customers',[MerchantController::class,'subscribers']);
    Route::get('/v2/{id}/programs',[MerchantController::class,'programs']);
    Route::get('/v2/{id}/programs/active',[MerchantController::class,'activePrograms']);
    Route::get('/v2/{id}/programs/inactive',[MerchantController::class,'inactivePrograms']);

    Route::resource('/v2/programs', ProgramController::class);
    
    Route::get('/v2/points/{id}/get-points',[MerchantController::class,'getPoints']);
    Route::get('/v2/points/{id}/redeemed',[MerchantController::class,'pointsRedeemed']);
    Route::get('/v2/points/{id}/unredeemed',[MerchantController::class,'unRedeemedPoints']);
    Route::get('/v2/points/{id}/expired',[MerchantController::class,'expiredPoints']);

    Route::get('/v2/merchants/{id}',[MerchantController::class,'show']);
    Route::put('/v2/merchants/{id}/update',[MerchantController::class,'update']); 
    Route::delete('/v2/merchants/{id}/delete',[MerchantController::class,'destroy']);
    
   
});
Route::group(['middleware' => ['auth:sanctum','role:customer']], function () {
    Route::post('/v2/subscriptions',[SubscriptionController::class,'store']);
    Route::get('/v2/subscriptions/{userid}', [SubscriptionController::class,'index']);
    Route::get('/v2/subscriptions/{id}/{userid}',[SubscriptionController::class,'show']);
    Route::put('/v2/subscriptions/{id}/{userid}/update',[SubscriptionController::class,'update']); 
    Route::put('/v2/subscription/{id}/{user_id}/unsubscribe/update',[SubscriptionController::class,'unsubscribe']); 
    Route::delete('/v2/subscription/{id}/delete',[SubscriptionController::class,'destroy']);
    
    Route::post('/v2/points/earn',[TransactionController::class,'earn']);
    Route::post('/v2/points/spend',[TransactionController::class,'spend']);
    Route::get('/v2/points/{id}',[TransactionController::class,'getPoints']); 
    Route::get('/v2/points/{id}/earned',[TransactionController::class,'getPointsEarned']);
    Route::get('/v2/points/{id}/spent',[TransactionController::class,'getPointsSpent']);
    Route::get('/v2/points/{id}/balance',[TransactionController::class,'getPoints']);
});
Route::group(['middleware' => ['auth:sanctum','role:customer|admin']], function () {
    Route::get('/v2/customers/{id}',[CustomerController::class,'show']);
    Route::put('/v2/customers/{id}/update',[CustomerController::class,'update']); 
    Route::delete('/v2/customers/{id}/delete',[CustomerController::class,'destroy']); 
});

Route::group(['middleware' => ['auth:sanctum','role:merchant|customer']], function () {
    Route::get('/v2/programs', [ProgramController::class,'index']);
    Route::get('/v2/programs/{id}',[ProgramController::class,'show']);
    Route::get('/v2/merchants',[MerchantController::class,'index']);
});

Route::group(['middleware' => ['auth:sanctum','role:admin']], function () {
    Route::get('/v2/customers',[CustomerController::class,'index']);
});





