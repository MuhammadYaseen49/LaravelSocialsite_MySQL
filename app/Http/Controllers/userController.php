<?php

namespace App\Http\Controllers;

use App\Http\Requests\userLogIn;
use App\Http\Requests\userRegistration;
use App\Http\Resources\userResource;
use App\Jobs\emailRegistration;
use App\Models\User;
use App\Models\Token;
use App\Services\GenerateToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Throwable;

class UserController extends Controller
{
    public function register(userRegistration $request)
    {
        try {
            $fields = $request->validated();
            
            $token = (new GenerateToken)->createToken($fields['email']);
            $url = 'http://127.0.0.1:8000/api/emailVerification/' . $token . '/' . $request->email;
            $user = User::create([
                'name' => $fields['name'],
                'email' => $fields['email'],
                'password' => Hash::make($fields['password']),
                'email_verified_at' => null,
                'url' => $url
            ]);
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
                    'message' => 'Eamil Varified'
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

    public function login(userLogIn $request)
    {
        try {
            $fields = $request->validated();

            $user = User::where('email', $fields['email'])->first();
            if (isset($user->id)) {

                if (Hash::check($fields['password'], $user->password)) {
                
                    $isLoggedIn = Token::where('userID', $user->id)->first();
                    if ($isLoggedIn) {
                        return response([
                            "message" => "User already logged In",
                        ], 400);
                    }

                    $token = (new GenerateToken)->createToken($user->id);
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
            $userID = decodingUserID($request);
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

    public function seeProfile(Request $request)
    {
        try {
            $userID = decodingUserID($request);
            $check = Token::where('token', $request->bearerToken())->first();
            if (!isset($check)) {
                return response([
                    "message" => "Invalid Token"
                ], 200);
            }

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
