<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class CustomerResource extends JsonResource
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
            'email' => $this->userCustomer($this->user_id)['email'],
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_prefix' => $this->userCustomer($this->user_id)['phone_prefix'],
            'phone_number' => $this->userCustomer($this->user_id)['mobile'],
            'user_type' => $this->user_type
        ];
    }
}
