<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantResource extends JsonResource
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
            'name' => $this->merchant_name,
            'description' => $this->merchant_name,
            'email' => $this->userMerchant($this->user_id)['email'],
            'phone_prefix' => $this->userMerchant($this->user_id)['phone_prefix'],
            'phone_number' => $this->userMerchant($this->user_id)['mobile'],
            'user_type' => $this->user_type,
            'location' => $this->location,
        ];
    }
}
