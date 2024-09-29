<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Customer;
use App\Models\Merchant;

class LoginResource extends JsonResource
{
   
    public function toArray($request)
    {
        return [
            'message' => "success",
            'access_token' => $this->access_token,
            'token_type' => "Bearer",
            "user" => $this->data,
            "expires_in" => $this->expires_in
        ];
    }
}
