<?php
namespace App\Services;

use Firebase\JWT\JWT;
use Throwable;

class GenerateToken {
    public function createToken($id)
    {
        try {
            $key = config("constants.KEY");
            $payload = array(
                "iss" => "http://127.0.0.1:8000",
                "aud" => "http://127.0.0.1:8000/api",
                "iat" => time(),
                "nbf" => 1357000000,
                "exp" => time() + 10000,
                "id" => $id
            );
            $jwt = JWT::encode($payload, $key, 'HS256');
            return $jwt;
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }
}