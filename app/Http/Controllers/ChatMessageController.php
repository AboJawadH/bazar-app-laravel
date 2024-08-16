<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use App\Notifications\ChatMessageCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ChatMessageController extends Controller
{
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@           CREATE           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function sendMessage(Request $request)
    {
        Log::debug("This Function Is Send Message");
        Log::debug("0");
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        $validator = Validator::make($request->all(), [
            'chat_id' => "required|integer|exists:chats,id",
            'sender_id' => "required|integer|exists:users,id",
            'sender_name' => 'nullable|string',
            'text' => 'nullable|string',
            'image' => 'nullable|string',
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


        if ($request->filled("image")) {
            $base64Image = $request->input('image');

            // Decode the base64 string into binary image data
            $imageData = base64_decode($base64Image);
            // Generate a unique filename for the image
            $imageName = Str::uuid() . '.' . "png";
            // Specify the storage path where you want to save the image
            Storage::disk('public')->put('chat-images/' . $imageName, $imageData);
            $imageUrl = asset('storage/chat-images/' . $imageName);
        }

        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        $message = ChatMessage::create([
            'chat_id' => $validatedData['chat_id'],
            'sender_id' => $validatedData['sender_id'],
            'sender_name' => $validatedData['sender_name'],
            'text' => $validatedData['text'],
            'image' => $imageUrl ?? null,
            'is_read' => false,
        ]);
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@// notifications
        //@@@@@@@@@@@@@//
        $chat = Chat::findOrFail($validatedData['chat_id']);
        $senderId = $validatedData['sender_id'];
        if ($chat->user_one_id == $senderId) {
            $receiverId = $chat->post_publisher_id;
        } else {
            $receiverId = $chat->user_one_id;
        }
        $messageSender = User::findOrFail($validatedData['sender_id']);
        $messageReceiver = User::findOrFail($receiverId);
        $messageReceiver->notify(new ChatMessageCreatedNotification($messageSender, $validatedData['chat_id']),);
        Log::debug("4");


        return response()->json([
            'status' => true,
            'message' => 'chat created successfully',
            'chat_message' => $message,
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@           DELETE           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "message_id" => "required|integer|exists:chat_messages,id",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();

        $message = ChatMessage::find($validatedData['message_id']);

        // foreach ($post->medias as $media) {
        //     $oldImagePath = public_path('storage/post-images/' . basename($media->path));
        //     if (file_exists($oldImagePath)) {
        //         unlink($oldImagePath);
        //     }
        // }

        $message->delete();

        return response()->json([
            'status' => true,
            'message' => 'Message deleted successfully',
        ]);
    }
}
