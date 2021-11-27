<?php

namespace App\Http\Controllers;

use App\Http\Requests\postCreate;
use App\Http\Resources\postResource;
use App\Models\Post;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function createPost(postCreate $request){
        try {
            $request->validated();
            $userID = decodingUserID($request);
            $post = new Post();
           
            if(($post != null)){
                if($request->title == null || $request->body == null || $request->privacy == null){
                    return response([
                        "status" => 0,
                        "Error" => "Title, Body and Privacy fields are required!"
                    ]);
                }
                
                if($request->privacy != null){
                    if ($request->privacy == "public" || $request->privacy == "Public" ||
                        $request->privacy == "private" || $request->privacy == "Private") {
                    
                            $privacy =  $request->privacy;
                    
                    } else {
                        return response([
                            "status" => 0,
                            'Privacy' => 'Enter Public or Private'
                        ]);
                    }
                }
            }

            $post->user_id = $userID;
            $post->title = $request->title;
            $post->body = $request->body;
            $post->attachment = $request->attachment;
            $post->privacy = $privacy;
            $post->save();

            return response([
                    "status" => 1,
                    "message" => "Post created!"
            ]);
            
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    public function allPosts()
    {
        try {
            $posts = Post::all();
            return response()->json([
                "status" => "1",
                "Posts" => new postResource($posts)
            ], 200);
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    public function myPost(Request $request)
    {
        try {
            $userID = decodingUserID($request);
            $my_posts = Post::all()->where("user_id", $userID);
            if ($my_posts) {
                return response()->json([
                    "status" => "1",
                    "message" => "Post found!",
                    "Profile" => new postResource($my_posts)
                ]);
            } else {
                return response()->json([
                    "status" => "0",
                    "message" => "Post not found!",
                ], 404);
            }
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    public function updatePost(Request $request, $id)
    {
        try {
            //get token from header and check user id
            $userID = decodingUserID($request);
            $post = Post::all()->where('user_id', $userID)->where('id', $id)->first();
            if (isset($post)) {
                $post->update($request->all());
                //message on Successfully
                return response([
                    'Status' => '200',
                    'message' => 'you have successfully Update Post',
                ]);
            } else {
                //message on Unauthorize
                return response([
                    'message' => 'you are unuthorize to Update other User Posts',
                ]);
            }
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    public function deletePost(Request $request, $id)
    {
        try {
            //get token from header and check user id
            $userID = decodingUserID($request);
            $delete_post = Post::all()->where('user_id', $userID)->where('id', $id)->first();

            if (isset($delete_post)) {
                $delete_post->delete($id);
                return response([
                    'Status' => '200',
                    'message' => 'you have successfully Deleted the Post',
                    'Deleted Post ID' => $id
                ]);
            } else {
                return response([
                    'message' => 'you are unauthorize to delete other User Posts'
                ]);
            }
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }
}
