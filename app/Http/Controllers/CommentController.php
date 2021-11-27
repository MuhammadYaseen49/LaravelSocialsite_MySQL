<?php

namespace App\Http\Controllers;

use App\Http\Requests\commentCreate;
use App\Http\Resources\commentResource;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function createComment(commentCreate $request)
    {
        try {
            $request->validated();
            $userID = decodingUserID($request);
           
            $postExist = Post::where('id', $request->post_id)->first();

            if (isset($postExist)) {
                if ($postExist->privacy == 'Public' || $postExist->privacy == 'public') {
                    $attachment = null;
                    if ($request->file('attachment') != null) {
                        $attachment = $request->file('attachment')->store('commentsAttachments');
                    }

                    $comment = Comment::create([
                        'user_id' => $userID,
                        'post_id' => $request->post_id,
                        'comments' => $request->comments,
                        'attachment' => $attachment
                    ]);

                    if (isset($comment)) {
                        return response([
                            'message' => 'Comment Created Successfully',
                            'Comment' => new commentResource($comment)
                        ]);
                    } else {
                        return response([
                            'message' => 'Something Went Wrong'
                        ]);
                    }
                }
                 elseif ($postExist->privacy == 'Private' || $postExist->privacy == 'private') {
                    $user = DB::select('select * from friends where ((sender_id = ? AND reciver_id = ?) OR (sender_id = ? AND reciver_id = ?)) AND status = ?', [$postExist->user_id, $userID, $postExist->user_id, 'Accept']);
                    if (!empty($user)) {
                        $attachment = null;
                        if ($request->file('attachment') != null) {
                            $attachment = $request->file('attachment')->store('commentsAttachments');
                        }

                        $comment = Comment::create([
                            'user_id' => $userID,
                            'post_id' => $request->post_id,
                            'comments' => $request->comments,
                            'attachment' => $attachment
                        ]);

                        if (isset($comment)) {
                            return response([
                                'message' => 'Comment Created Successfully',
                                'Comment' => new commentResource($comment)
                            ]);
                        } else {
                            return response([
                                'message' => 'Something Went Wrong',
                            ]);
                        }
                    } else {
                        return response([
                            'message' => 'This is a Private Post. You are unuthorize to Comment on this Post',
                        ]);
                    }
                }
            } else {
                return response([
                    'message' => 'No Post Found'
                ]);
            }
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }


    public function updateComment(Request $request, $id)
    {
        try {
            $userID = decodingUserID($request);

            $commentExist = Comment::where('user_id', $userID)->where('id', '=', $id)->first();
            if (isset($commentExist)) {
                $postPrivacy = POST::where('id', $commentExist->post_id)->first();
                if ($postPrivacy->privacy == 'Public' || $postPrivacy->privacy == 'public') {
                    if ($request->file('attachment') != null && $commentExist->attachment != null) {
                        unlink(storage_path('app/' . $commentExist->attachment));
                    }
                    $commentExist->update($request->all());

                    if ($request->file('attachment') != null) {
                        $commentExist->attachment = $request->file('attachment')->store('commentsAttachments');
                        $commentExist->save();
                    }
                    return response([
                        'Updation' => new commentResource($commentExist)
                    ]);
                } elseif ($postPrivacy->privacy == 'Private' || $postPrivacy->privacy == 'private') {
                    $userSeen = DB::select('select * from friend_requests where ((sender_id = ? AND reciever_id = ?) OR (sender_id = ? AND reciever_id = ?)) AND status = ?', [$postPrivacy->user_id, $userID, $postPrivacy->user_id, 'Accept']);
                    if (!empty($userSeen)) {
                        if ($request->file('attachment') != null) {
                            unlink(storage_path('app/' . $commentExist->attachment));
                        }
                        $commentExist->update($request->all());
                        if ($request->file('attachment') != null) {
                            $commentExist->attachment = $request->file('attachment')->store('commentsAttachments');
                            $commentExist->save();
                        }
                        return response([
                            'Updation' =>  new commentResource($commentExist)
                        ]);
                    } else {
                        return response([
                            'message' => 'This Post is Private and you are not a friend'
                        ]);
                    }
                }
            } else {
                return response([
                    'message' => 'No Post Found',
                ]);
            }
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    public function deleteComment(Request $request, $id)
    {
        try {
            $userID = decodingUserID($request);

            $comment = Comment::where('id', $id, 'AND', 'user_id', $userID)->first();
            if (isset($comment)) {
                if ($comment->attachment != null) {
                    unlink(storage_path('app/' . $comment->attachment));
                }
                $comment->delete();
                return response([
                    'message' => 'Comment has been Deleted',
                ]);
            } else {
                return response([
                    'message' => 'You are Unauthorize to Delete Comment',
                ]);
            }
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }
}
