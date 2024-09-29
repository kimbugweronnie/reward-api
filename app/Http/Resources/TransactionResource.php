<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'transaction_amount'=> $this->transaction_amount,
            'payment_mode'=>$this->payment_mode,
            'points_used'=> $this->points_awarded,
            'points_used'=> $this->points_used,
            'receipt_number'=>$this->receipt_number,
            'subscription_id'=>$this->subscription_id,
            'user_id'=>$this->user_id,
        ];
    }
}
