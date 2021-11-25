<?php

namespace App\Http\Controllers;
use App\Models\Post;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function createPost(Request $request){ 

        $request->validate([
            "title"=>"required",
            "body"=>"required",
            "privacy"=>"required"
        ]);

        $getToken = $request->bearerToken();
        $decoded = JWT::decode($getToken, new Key("ProgrammersForce", "HS256"));
        $userID = $decoded->data;

        $post = new Post();

        $post->user_id = $userID;
        $post->title = $request->title;
        $post->body = $request->body;
        $post->attachment = $request->attachment;
        $post->privacy = $request->privacy;
        $post->save();

        return response([
            "status"=> 1,
            "message"=>"Post created!"
        ]);

    }

    public function listPost(){
        $posts = Post::all();
        return response()->json([
            "status"=>"1",
            "message"=>"Listing Posts",
            "data"=> $posts
        ], 200);
    }

    public function myPost(Request $request){
        $getToken = $request->bearerToken();
        $decoded = JWT::decode($getToken, new Key("ProgrammersForce", "HS256"));
        $userID = $decoded->data;
        $my_posts = Post::all()->where("user_id", $userID);
        if($my_posts){
            return response()->json([
                "status"=>"1",
                "message"=>"Post found!",
                "data"=>$my_posts
            ]);
        }else{
            return response()->json([
                "status"=>"0",
                "message"=>"Post not found!",
            ], 404);


        }
    }

    public function updatePost(Request $request, $id)
    {
        //get token from header and check user id
        $getToken = $request->bearerToken();
        $decoded = JWT::decode($getToken, new Key("ProgrammersForce", "HS256"));
        $userID = $decoded->data;
  
        $post = Post::all()->where('user_id',$userID)->where('id' , $id)->first();
        if(isset($post)){
            $post->update($request->all());
            //message on Successfully
            return response([
                'Status' => '200',
                'message' => 'you have successfully Update Post',
            ], 200);
        }else{
            //message on Unauthorize
            return response([
                'Status' => '200',
                'message' => 'you are not Authorize to Update other User Posts',
            ], 200);
        }
    }

    public function deletePost(Request $request, $id)
    {
        //get token from header and check user id
        $getToken = $request->bearerToken();
        $decoded = JWT::decode($getToken, new Key("ProgrammersForce", "HS256"));
        $userID = $decoded->data;
        $delete_post = Post::all()->where('user_id',$userID)->where('id' , $id)->first();

        if (isset($delete_post)) {
            $delete_post->delete($id);
            return response([
                'Status' => '200',
                'message' => 'you have successfully Deleted Entry',
                'Deleted Post ID' => $id
            ], 200);
        }else {
            return response([
                'Status' => '201',
                'message' => 'you are Authorize to delete other User Posts'
            ], 200);
        }
    }


}
