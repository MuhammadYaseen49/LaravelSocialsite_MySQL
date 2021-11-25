<?php

namespace App\Http\Controllers;

use App\Http\Requests\postCreate;
use App\Http\Resources\postResource;
use App\Models\Post;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function createPost(postCreate $request){
        try {
            $request->validated();

            $getToken = $request->bearerToken();
            $key = config("constants.KEY");
            $decoded = JWT::decode($getToken, new Key($key, "HS256"));
            $userID = $decoded->data;

            $post = new Post();

            $post->user_id = $userID;
            $post->title = $request->title;
            $post->body = $request->body;
            $post->attachment = $request->attachment;
            $post->privacy = $request->privacy;
            $post->save();

            return response([
                "status" => 1,
                "message" => "Post created!"
            ]);
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    public function listPost()
    {
        try {
            $posts = Post::all();
            return response()->json([
                "status" => "1",
                "message" => "Listing Posts",
                "data" => new postResource($posts)
            ], 200);
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    public function myPost(Request $request)
    {
        try {
            $getToken = $request->bearerToken();
            $key = config("constants.KEY");
            $decoded = JWT::decode($getToken, new Key($key, "HS256"));
            $userID = $decoded->data;
            $my_posts = Post::all()->where("user_id", $userID);
            if ($my_posts) {
                return response()->json([
                    "status" => "1",
                    "message" => "Post found!",
                    "data" => new postResource($my_posts)
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
            $getToken = $request->bearerToken();
            $key = config("constants.KEY");
            $decoded = JWT::decode($getToken, new Key($key, "HS256"));
            $userID = $decoded->data;

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
                    'Status' => '200',
                    'message' => 'you are not Authorize to Update other User Posts',
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
            $getToken = $request->bearerToken();
            $decoded = JWT::decode($getToken, new Key("ProgrammersForce", "HS256"));
            $userID = $decoded->data;
            $delete_post = Post::all()->where('user_id', $userID)->where('id', $id)->first();

            if (isset($delete_post)) {
                $delete_post->delete($id);
                return response([
                    'Status' => '200',
                    'message' => 'you have successfully Deleted Entry',
                    'Deleted Post ID' => $id
                ]);
            } else {
                return response([
                    'Status' => '201',
                    'message' => 'you are Authorize to delete other User Posts'
                ]);
            }
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }
}
