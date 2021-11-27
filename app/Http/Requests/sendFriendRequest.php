<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class sendFriendRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'reciver_id' => 'required'
        ];
    }
}
