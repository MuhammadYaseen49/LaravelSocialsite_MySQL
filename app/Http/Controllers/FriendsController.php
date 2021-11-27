<?php

namespace App\Http\Controllers;

use App\Http\Requests\acceptFriendRequest;
use App\Http\Requests\sendFriendRequest;
use App\Models\Friends;
use Illuminate\Http\Request;
use App\Models\User;

class FriendsController extends Controller
{
    public function sendRequest(sendFriendRequest $request)
    {
        try {
            $request->validated();
            $userID = decodingUserID($request);

            if ($userID == $request->receiver_id) {
                return response([
                    "Message" => "You cannot Send Friend Request to yourself"
                ]);
            }
            $users = User::where('id', $request->receiver_id)->first();
            $data = new Friends();

            $checked = Friends::where('sender_id', $userID)->where('receiver_id', $request->receiver_id)->first();

            if (isset($checked)) {
                return response([
                    "Message" => "You have already Sent the Friend Request"
                ]);
            }

            if (isset($users)) {
                $data->receiver_id = $request->receiver_id;
                $data->sender_id = $userID;
                $data->save();

                return response([
                    "Message" => "You have Successfully Send Friend Request"
                ]);
            } else {
                return response([
                    "Message" => "This User Does not Exist in Records"
                ]);
            }
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    public function myRequests(Request $request)
    {
        try {
            $userID = decodingUserID($request);
            $findRequest = Friends::all()->where('receiver_id',  $userID)->where('status', '0');

            if (isset($findRequest)) {
                return $findRequest;
            }
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    public function acceptRequest(acceptFriendRequest $request)
    {
        try {
            $request->validated();
            $userID = decodingUserID($request);

            if ($userID == $request->sender_id) {
                return response([
                    "Message" => "You cannot receive friend request of yourself"
                ]);
            }

            $receiveRequest = Friends::where('sender_id', $request->sender_id)->where('receiver_id', $userID)->first();

            if ($receiveRequest->status == '1') {
                return response([
                    "Message" => "You are already friend of this u"
                ]);
            }

            if (isset($receiveRequest)) {
                $receiveRequest->status = '1';
                $receiveRequest->save();
                return response([
                    "Message" => "Congratulations! You are Friends Now"
                ]);
            } else {
                return response([
                    "Message" => "This User Does not Send you Friend Request"
                ]);
            }
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }
}
