<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerLoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'token' => $this->token ? $this->token : null,
            'user_type' => $this->usermerchant($this->id) ? $this->usermerchant($this->id)->user_type : $this->usercustomer($this->id)->user_type,
            'token_expiry' => $this->token_expiry,
            // 'programs' => $this->programs,
            // 'merchants' => $this->merchants,
            // 'points' => $this->points,
        ];
    }
}
