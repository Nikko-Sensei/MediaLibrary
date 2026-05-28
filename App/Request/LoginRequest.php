<?php

namespace App\Request;

class LoginRequest
{
    public function rules(): array
    {
        return [

            'username_or_email' => [
                'required' => true,
                'min' => 3,
            ],

            'password' => [
                'required' => true,
                'min' => 6,
            ],
        ];
    }
}