<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function index(Request $request)
    {
        Log::debug("this function is fetch all notifications");
        Log::debug("0");
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        $validator = Validator::make($request->all(), [
            "user_id" => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }
        Log::debug($validator->errors());
        Log::debug("1");
        $validatedData = $validator->validated();
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        $user = User::find($validatedData['user_id']);
        Log::debug("2");

        if ($user) {
            $notifications = $user->notifications()->latest()->get();
            Log::debug("3");

            // Optional: Filter based on read status (if applicable)
            // $unreadNotifications = $user->notifications()->whereNull('read_at')->get();
        } else {
            Log::debug("4");
            // Handle case where user is not found
        }

        return response()->json([
            'status' => true,
            'notifications' => $this->formatNotifications($notifications),
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    private function formatNotifications(Collection $notifications): array
    {
        $formattedNotifications = [];
        foreach ($notifications as $notification) {
            $formattedNotifications[] = [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? '', // Handle potential missing title
                'en_title' => $notification->data['en_title'] ?? '', // Handle potential missing title
                'tr_title' => $notification->data['tr_title'] ?? '', // Handle potential missing title
                'body' => $notification->data['body'] ?? "",
                'en_body' => $notification->data['en_body'] ?? "",
                'tr_body' => $notification->data['tr_body'] ?? "",
                'post_id' => $notification->data['post_id'] ?? null,
                'user_id' => $notification->data['user_id'] ?? null,
                'created_at' => $notification->created_at->format('Y-m-d'), // Format timestamp for Flutter
                // Add other relevant notification data fields here
            ];
        }
        return $formattedNotifications;
    }
}
