<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
   
    public function toArray($request)
    {
        return [
            'email' => $this->email,
            'message' => "User created and verfication email sent to $this->email"
        ];
    }
}
