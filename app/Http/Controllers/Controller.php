<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendResponse($result, $status)
    {
    	$response = ['message' => 'success', 'data'=> $result,'status' => $status];
        return response()->json($response, 201);
    }

    public function loginResponse($access_token,$user,$expires_in,$status)
    {
    	$response = ['message' => 'success','access_token' => $access_token, 'token_type' =>'Bearer','user'=> $user,'expires_in' => $expires_in,'status' => $status];
        return response()->json($response, 201);
    }
    
    public function sendMessage($result, $status)
    {
    	$response = [
            'message' => 'error',
            'data'    => $result,
            'status' => $status,
        ];
        return response()->json($response, $status);
    }

    public function sendMessageSubscription($result, $status)
    {
    	$response = [
            'message' => $result,
            'status' => $status,
        ];
        return response()->json($response, $status);
    }

    public function messageSubscription($result, $status)
    {
    	$response = [
            'message' => $result,
            'status' => $status,
        ];
        return response()->json($response, $status);
    }

    public function validationResponse($result, $status)
    {
    	$response = [
            'success' => 'error',
            'data'    => $result,
            'status' => $status,
        ];
        return response()->json($response, 422);
    }

    public function sucessMessage($result, $status)
    {
    	$response = [
            'success' => 'error',
            'data'    => $result,
            'status' => $status,
        ];
        return response()->json($response, 500);
    }

}
