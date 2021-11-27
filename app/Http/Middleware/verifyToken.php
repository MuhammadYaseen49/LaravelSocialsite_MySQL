<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\Models\Token;
use Illuminate\Http\Request;

class verifyToken
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $userID = decodingUserID($request);
            $userExist = Token::where("userID", $userID)->first();
            if (!isset($userExist)) {
                return response([
                    "message" => "Token does not exist!"
                ]);
            } else {
                return $next($request);
            }
        } catch (Exception $e) {
        }
    }
}
