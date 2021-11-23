<?php

namespace App\Http\Controllers;

use App\Mail\VarifyMail;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){}

    //Register Action
    public function register(Request $request){
        //Validate the fields
        $fields = $request->validate(
            [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|confirmed',
            ]);

        $token = $this->createToken($fields['email']);
        $url = 'http://127.0.0.1:8000/api/emailVarification/' .$token. '/' .$request->email;

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
            'email_verified_at' => null,
            'url'=> $url
        ]);
        // send email with the template
        Mail::to($request->email)->send(new VarifyMail($user->url));
        return $user;
    }

    public function emailVerification($token,$email){
  
        $emailVerify = User::where('email',$email)->first();
        if($emailVerify->email_verified_at != null){
            return response([
            'message'=>'Already Varified'
        ]);
        }else if ($emailVerify) {
            $emailVerify->email_verified_at = date('Y-m-d h:i:s');
            $emailVerify->save();
            return response([
        '   message'=>'Eamil Varified'
        ]);
        }else{
            return response([
            'message'=>'Error'
        ]);
        }
       
    }

    public function login(Request $request){
        $request = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        // Check Student
        $user = User::where('email', $request['email'])->first();
        // dd($user->id);
        if(isset($user->id)){
            
            if (Hash::check($request['password'], $user->password)) {
                // Create Token

                //Checking Token 
                $isLoggedIn = Token::where('userID', $user->id)->first();
                if($isLoggedIn){
                    return response([
                        "message" => "User already logged In",
                    ], 400);
                }       
                
                $token = $this->createToken($user->id);
                // saving token table in db
                $saveToken = Token::create([
                    "userID"=>$user->id,
                    "token" => $token
                ]);
                $response = [
                    'status' => 1,
                    'message' => 'Logged in successfully',
                    'user' => $user,
                    'token' => $token
                ];
        
                return response($response, 201);
                
            }else{
                return response([
                    'message' => 'Invalid email or password'
                ], 401);
            }

        }else{
            return response()->json([
                "status"=>0,
                "message"=>"Student not found"
            ],404);
        
        } 
         
    }

    public function logout(Request $request){
        $getToken = $request->bearerToken(); 

        $decoded = JWT::decode($getToken, new Key("ProgrammersForce","HS256"));
        $userID = $decoded->data;
        $userExist = Token::where("userID",$userID)->first();
        if($userExist)
        {
            $userExist->delete();
        }

        return response([
            "message" => "logout successfull"
        ], 200);

    }

    public function createToken($data){
        $key = "ProgrammersForce";
        $payload = array(
            "iss" => "http://127.0.0.1:8000",
            "aud" => "http://127.0.0.1:8000/api",
            "iat" => time(),
            "nbf" => 1357000000,
            "exp" => time() + 1000,
            "data" => $data
        );
        $jwt = JWT::encode($payload, $key, 'HS256');
        return $jwt;
    }

    public function profile(Request $request){
        //get token from header
        $getToken = $request->bearerToken();
        
        // if token is invalid
        $check = Token::where('token' , $getToken)->first();
        if(!isset($check)){
            return response([
            "message" => "Invalid Token"
            ], 200);
        }
        $decoded = JWT::decode($getToken, new Key("ProgrammersForce", "HS256"));
        $userID = $decoded->data;
        if($userID) {

            $profile = User::find($userID);
            return response([
                "Details" => $profile
            ], 200);
        }
    }
}
