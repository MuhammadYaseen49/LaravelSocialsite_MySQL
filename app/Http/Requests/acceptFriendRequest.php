<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class acceptFriendRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'sender_id' => 'required'
        ];
    }
}
