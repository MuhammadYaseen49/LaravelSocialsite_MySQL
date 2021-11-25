<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class postResource extends JsonResource
{
    public function toArray($request)
    {
        $resources = $request->all();

        return[
            $resources
        ];
    }
}
