<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SingleCustomerResource extends JsonResource
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
            'phone_number' => $this->phone_number,
            'phone_prefix' => $this->phone_prefix,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'user_type' => $this->user_type,

        ];
    }
}
