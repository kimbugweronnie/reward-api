<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Subscription;

class ProgramResource extends JsonResource
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
            'name' => $this->name,
            'product' => $this->product,
            'percentage' => $this->percentage,
            'state_date' => $this->start_date,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'merchant_id' => $this->merchant_id,
            'users' => $this->programSubscribers($this->id)
        ];
    }
}
