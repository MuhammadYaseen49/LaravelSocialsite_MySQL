<?php

namespace App\Http\Controllers;

use App\Http\Requests\userRegistration;
use App\Http\Resources\userResource;
use App\Jobs\emailRegistration;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
use App\Models\Token;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Throwable;

class MainController extends Controller
{
    //Register Action
    public function register(userRegistration $request)
    {
        try {
            //Validate the fields
            $fields = $request->validated();

            $token = $this->createToken($fields['email']);
            $url = 'http://127.0.0.1:8000/api/emailVarification/' . $token . '/' . $request->email;

            $user = User::create([
                'name' => $fields['name'],
                'email' => $fields['email'],
                'password' => Hash::make($fields['password']),
                'email_verified_at' => null,
                'url' => $url
            ]);
            // send email with the template
            emailRegistration::dispatch($request->email, $url); //php artisan queue:work
            return new userResource($user);
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    public function emailVerification($token, $email)
    {
        try {
            $emailVerify = User::where('email', $email)->first();
            if ($emailVerify->email_verified_at != null) {
                return response([
                    'message' => 'Already Varified'
                ]);
            } else if ($emailVerify) {
                $emailVerify->email_verified_at = date('Y-m-d h:i:s');
                $emailVerify->save();
                return response([
                    '   message' => 'Eamil Varified'
                ]);
            } else {
                return response([
                    'message' => 'Error'
                ]);
            }
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    public function login(Request $request)
    {
        try {
            $fields = $request->validated();

            // Check Student
            $user = User::where('email', $fields['email'])->first();
            // dd($user->id);
            if (isset($user->id)) {

                if (Hash::check($fields['password'], $user->password)) {
                    // Create Token

                    //Checking Token 
                    $isLoggedIn = Token::where('userID', $user->id)->first();
                    if ($isLoggedIn) {
                        return response([
                            "message" => "User already logged In",
                        ], 400);
                    }

                    $token = $this->createToken($user->id);
                    // saving token table in db
                    $saveToken = Token::create([
                        "userID" => $user->id,
                        "token" => $token
                    ]);
                    $response = [
                        'status' => 1,
                        'message' => 'Logged in successfully',
                        'user' => new userResource($user),
                        'token' => $token
                    ];

                    return response($response, 201);
                } else {
                    return response([
                        'message' => 'Invalid email or password'
                    ], 401);
                }
            } else {
                return response()->json([
                    "status" => 0,
                    "message" => "Student not found"
                ], 404);
            }
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    public function logout(Request $request)
    {
        try {
            $getToken = $request->bearerToken();
            $key = config("constants.KEY");
            $decoded = JWT::decode($getToken, new Key($key, "HS256"));
            $userID = $decoded->data;
            $userExist = Token::where("userID", $userID)->first();
            if ($userExist) {
                $userExist->delete();
            }

            return response([
                "message" => "logout successfull"
            ]);
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    public function createToken($data)
    {
        try {
            $key = config("constants.KEY");
            $payload = array(
                "iss" => "http://127.0.0.1:8000",
                "aud" => "http://127.0.0.1:8000/api",
                "iat" => time(),
                "nbf" => 1357000000,
                "exp" => time() + 10000,
                "data" => $data
            );
            $jwt = JWT::encode($payload, $key, 'HS256');
            return $jwt;
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    public function seeProfile(Request $request)
    {
        try {
            //get token from header
            $getToken = $request->bearerToken();

            // if token is invalid
            $check = Token::where('token', $getToken)->first();
            if (!isset($check)) {
                return response([
                    "message" => "Invalid Token"
                ], 200);
            }
            $key = config("constants.KEY");
            $decoded = JWT::decode($getToken, new Key($key, "HS256"));
            $userID = $decoded->data;
            if ($userID) {

                $profile = User::find($userID);
                return response([
                    "Profile" => new userResource($profile)
                ], 200);
            }
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    public function updateProfile(Request $request, $id)
    {
        try {
            $user = User::all()->where('id', $id)->first();
            //message on Successfully
            if (isset($user)) {
                $user->update($request->all());
                return response([
                    'Status' => '200',
                    'message' => 'you have successfully Updated User Profile',
                ]);
            }
            if ($user == null) {
                return response([
                    'Status' => '404',
                    'message' => 'User not found',
                ]);
            }
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }
}
