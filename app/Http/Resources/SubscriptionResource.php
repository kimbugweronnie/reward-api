<?php

namespace App\Http\Resources;
use App\Models\Merchant;
use App\Models\User;
use App\Models\Program;


use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'card' => $this->card_number,
            'program' => Program::where('id',$this->program_id)->select('id','name','product','status')->first(),
            'merchant' => Merchant::where('id',$this->merchant_id)->select('id','merchant_name','merchant_description')->first(),
            'user' => User::where('id',$this->user_id)->select('id','email','mobile')->first()

        ];
       
    }
}
