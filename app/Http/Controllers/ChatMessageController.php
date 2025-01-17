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

        // Step 1: Validate the input
        $validator = Validator::make($request->all(), [
            'chat_id' => "required|integer|exists:chats,id",
            'sender_id' => "required|integer|exists:users,id",
            'sender_name' => 'nullable|string',
            'text' => 'nullable|string',
            'image' => 'nullable|string', // Base64-encoded image
            'voice_message' =>
            'nullable|file|mimetypes:audio/mpeg,audio/wav,audio/x-m4a|max:10240',
            // 'nullable|file|mimetypes:audio/mpeg,audio/wav|max:10240', // New validation rule for voice message
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();

        // Step 2: Process the image if provided
        $imageUrl = null;
        if ($request->filled("image")) {
            $base64Image = $request->input('image');
            $imageData = base64_decode($base64Image);
            $imageName = Str::uuid() . '.' . "png";
            Storage::disk('public')->put('chat-images/' . $imageName, $imageData);
            $imageUrl = asset('storage/chat-images/' . $imageName);
        }

        // Step 3: Process the voice message if provided
        $voiceMessageUrl = null;
        if ($request->hasFile('voice_message')) {
            $voiceMessageFile = $request->file('voice_message');
            $voiceMessageName = Str::uuid() . '.' . $voiceMessageFile->getClientOriginalExtension();
            $voiceMessagePath = $voiceMessageFile->storeAs('chat-voice-messages', $voiceMessageName, 'public');
            $voiceMessageUrl = asset('storage/' . $voiceMessagePath);
        }

        // Step 4: Create the chat message
        $message = ChatMessage::create([
            'chat_id' => $validatedData['chat_id'],
            'sender_id' => $validatedData['sender_id'],
            'sender_name' => $validatedData['sender_name'],
            'text' => $validatedData['text'],
            'image' => $imageUrl,
            'voice_message' => $voiceMessageUrl, // Save voice message URL
            'is_read' => false,
        ]);

        // Step 5: Send notifications
        $chat = Chat::findOrFail($validatedData['chat_id']);
        $senderId = $validatedData['sender_id'];
        $receiverId = ($chat->user_one_id == $senderId) ? $chat->post_publisher_id : $chat->user_one_id;
        $messageSender = User::findOrFail($senderId);
        $messageReceiver = User::findOrFail($receiverId);
        $messageReceiver->notify(new ChatMessageCreatedNotification($messageSender, $validatedData['chat_id']));

        // Step 6: Return the response
        return response()->json([
            'status' => true,
            'message' => 'Chat message sent successfully',
            'chat_message' => $message,
        ]);
    }
    // public function sendMessage(Request $request)
    // {
    //     Log::debug("This Function Is Send Message");
    //     Log::debug("0");
    //     //@@@@@@@@@@@@@//
    //     //@@@@@@@@@@@@@//
    //     //@@@@@@@@@@@@@//
    //     $validator = Validator::make($request->all(), [
    //         'chat_id' => "required|integer|exists:chats,id",
    //         'sender_id' => "required|integer|exists:users,id",
    //         'sender_name' => 'nullable|string',
    //         'text' => 'nullable|string',
    //         'image' => 'nullable|string',
    //     ]);

    //     Log::debug("1");
    //     Log::debug($validator->errors());
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Wrong parameters',
    //             'errors' => Arr::flatten($validator->errors()->toArray()),
    //             // 'errors' => $validator->errors(),
    //         ]);
    //     }
    //     Log::debug("2");
    //     $validatedData = $validator->validated();
    //     Log::debug("3");


    //     if ($request->filled("image")) {
    //         $base64Image = $request->input('image');

    //         // Decode the base64 string into binary image data
    //         $imageData = base64_decode($base64Image);
    //         // Generate a unique filename for the image
    //         $imageName = Str::uuid() . '.' . "png";
    //         // Specify the storage path where you want to save the image
    //         Storage::disk('public')->put('chat-images/' . $imageName, $imageData);
    //         $imageUrl = asset('storage/chat-images/' . $imageName);
    //     }

    //     //@@@@@@@@@@@@@//
    //     //@@@@@@@@@@@@@//
    //     //@@@@@@@@@@@@@//
    //     $message = ChatMessage::create([
    //         'chat_id' => $validatedData['chat_id'],
    //         'sender_id' => $validatedData['sender_id'],
    //         'sender_name' => $validatedData['sender_name'],
    //         'text' => $validatedData['text'],
    //         'image' => $imageUrl ?? null,
    //         'is_read' => false,
    //     ]);
    //     //@@@@@@@@@@@@@//
    //     //@@@@@@@@@@@@@// notifications
    //     //@@@@@@@@@@@@@//
    //     $chat = Chat::findOrFail($validatedData['chat_id']);
    //     $senderId = $validatedData['sender_id'];
    //     if ($chat->user_one_id == $senderId) {
    //         $receiverId = $chat->post_publisher_id;
    //     } else {
    //         $receiverId = $chat->user_one_id;
    //     }
    //     $messageSender = User::findOrFail($validatedData['sender_id']);
    //     $messageReceiver = User::findOrFail($receiverId);
    //     $messageReceiver->notify(new ChatMessageCreatedNotification($messageSender, $validatedData['chat_id']),);
    //     Log::debug("4");


    //     return response()->json([
    //         'status' => true,
    //         'message' => 'chat created successfully',
    //         'chat_message' => $message,
    //     ]);
    // }

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
