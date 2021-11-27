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
                "Profile" => new postResource($posts)
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
            $userID = decodingUserID($request);
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
