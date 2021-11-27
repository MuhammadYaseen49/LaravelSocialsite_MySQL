<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class postResource extends JsonResource
{
    public function toArray($request)
    {  
        // return parent::toArray($request);

        //With Resource
        return [
            'Title' => $this->title,
            'Body' => $this->body,
            'Attachment' => $this->attachment,
            'Privacy' => $this->privacy
        ];
    }
}
