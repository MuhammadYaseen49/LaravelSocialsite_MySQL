<?php

namespace App\Http\Middleware;

use App\Models\Token;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class verifyToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $getToken = $request->bearerToken(); 
        $decoded = JWT::decode($getToken, new Key("ProgrammersForce","HS256"));
        $userID = $decoded->data;
        $userExist = Token::where("userID",$userID)->first();
        if(!isset($userExist))
        {
            return response([
                "message" => "Token does not exist!"
            ], 404);     
        }
        else{
            return $next($request);  
            
        }

       




    }
}
