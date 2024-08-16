<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatResource;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class ChatController extends Controller
{
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@           CREATE           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getOrCreateChat(Request $request)
    {
        Log::debug("This Function Is Get Or Create Chat");
        Log::debug("0");
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        $validator = Validator::make($request->all(), [
            'user_one_id' => "required|integer|exists:users,id",
            'post_publisher_id' => "required|integer|exists:users,id",
        ]);

        Log::debug("1");
        Log::debug($validator->errors());
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
                // 'errors' => $validator->errors(),
            ]);
        }
        Log::debug("2");
        $validatedData = $validator->validated();
        Log::debug("3");
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@// GET
        //@@@@@@@@@@@@@//
        $userOneId = $validatedData['user_one_id'];
        $userTwoId = $validatedData['post_publisher_id'];

        $chat = Chat::with("messages", "userOne", "userTwo")->where(function ($query) use ($userOneId, $userTwoId) {
            $query->where('user_one_id', $userOneId)
                ->where('post_publisher_id', $userTwoId);
        })->orWhere(function ($query) use ($userOneId, $userTwoId) {
            $query->where('user_one_id', $userTwoId)->where('post_publisher_id', $userOneId);
        })->first();

        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@// UPDATE IS READ STATUS
        //@@@@@@@@@@@@@//
        // if ($chat) {
        //     // ChatMessage::where('chat_id', $chat->id)
        //     //     ->where(function ($query) use ($userOneId) {
        //     //         $query->where('sender_id', '<>', $userOneId);
        //     //     })
        //     //     ->where('is_read', false)
        //     //     ->update(['is_read' => true]);
        //     ChatMessage::where('chat_id', $chat->id)
        //         ->where('sender_id', '!=', $userOneId)
        //         ->where('is_read', false)
        //         ->update(['is_read' => true]);
        //     Log::debug("4");
        // }

        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@// CREATE
        //@@@@@@@@@@@@@//
        if (!$chat) {
            Log::debug("ther is no chat creating one mow .......");
            $chat = Chat::create(
                [
                    'user_one_id' => $userOneId,
                    'post_publisher_id' => $userTwoId,
                ],
            );
        }
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        return response()->json([
            'status' => true,
            'message' => 'chat created successfully',
            'chat' =>  new ChatResource($chat),
        ]);
    }
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@             GET            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getAllChatsForUser(Request $request)
    {
        Log::debug("This Function Is Get All Chats");
        Log::debug("0");
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        $validator = Validator::make($request->all(), [
            'user_id' => "required|integer|exists:users,id",
        ]);

        Log::debug("1");
        Log::debug($validator->errors());
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
                // 'errors' => $validator->errors(),
            ]);
        }
        Log::debug("2");
        $validatedData = $validator->validated();
        Log::debug("3");
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        $userId = $validatedData['user_id'];

        $chats = Chat::with("messages", "userOne", "userTwo")
            ->where(function ($query) use ($userId) {
                $query->where('user_one_id', $userId)
                    ->orWhere('post_publisher_id', $userId);
            })
            ->has('messages')
            ->get();

        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        return response()->json([
            'status' => true,
            'message' => 'chats fetched successfully',
            'chats' => ChatResource::collection($chats),
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            DELETE          @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "chat_id" => "required|integer|exists:chats,id",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();

        $chat = Chat::find($validatedData['chat_id']);

        // foreach ($post->medias as $media) {
        //     $oldImagePath = public_path('storage/post-images/' . basename($media->path));
        //     if (file_exists($oldImagePath)) {
        //         unlink($oldImagePath);
        //     }
        // }

        $chat->delete();

        return response()->json([
            'status' => true,
            'message' => 'Chat deleted successfully',
        ]);
    }
}
