<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class userResource extends JsonResource
{ 
    public function toArray($request)
    {
        // return parent::toArray($request);

        //With Resource
        return [
            'Name' => $this->name,
            'Email' => $this->email,
            'Account Created at' => $this->email_verified_at
        ];
    }
}
