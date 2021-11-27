<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

function decodingUserID(Request $request)
{
    // dd("asaas");
    $getToken = $request->bearerToken();
    // dd($getToken);
    $key = config('constants.KEY');
    $decoded = JWT::decode($getToken, new Key($key, "HS256"));
    // dd($decoded);
    $userID = $decoded->id;
    // dd($userID);
    return $userID;

}
