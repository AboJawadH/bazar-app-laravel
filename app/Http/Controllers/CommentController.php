<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\CommentCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function index(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "post_id" => 'required|integer|exists:posts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();

        $post = Post::with("comments")->findOrFail($validatedData['post_id']);

        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            // 'post_object' => new PostResource($post), // Pass the object directly
            'post_object' => $post,
            // 'errors' => $validator->errors(),
        ]);
    }
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function fetchComments(Request $request)
    {
        Log::debug("this function is get all comments");
        Log::debug("0");
        $validator = Validator::make($request->all(), [
            "post_id" => 'required|integer|exists:posts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }
        Log::debug($validator->errors());

        $validatedData = $validator->validated();
        Log::debug("1");

        $comments = Comment::with('user')->where("post_id", $validatedData["post_id"])->get();
        Log::debug("2");
        Log::debug($comments->count());

        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            // 'post_object' => new PostResource($post), // Pass the object directly
            'comments' => CommentResource::collection($comments),
            // 'errors' => $validator->errors(),
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@           CREATE           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function store(Request $request)
    {
        Log::debug("this function is create a new comment");
        Log::debug("0");

        $validator = Validator::make($request->all(), [
            'comment_message' => 'required|string',
            'user_name' => 'required|string',
            'post_id' => 'required|integer|exists:posts,id',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();
        Log::debug("1");


        Log::debug("2");

        $comment =  Comment::create(
            [
                'comment_message' => $validatedData['comment_message'],
                'user_name' => $validatedData['user_name'],
                'post_id' => $validatedData['post_id'],
                'user_id' => $validatedData['user_id'],
            ],
        );

        Log::debug("3");

        $user = User::findOrFail($comment->user_id);
        $post = Post::findOrFail($comment->post_id);
        $postPublisher = User::findOrFail($post->user_id);
        Log::debug("4");

        $postPublisher->notify(new CommentCreatedNotification($comment, $post));
        Log::debug("5");

        return response()->json([
            'status' => true,
            'message' => 'data successfully created',
            'comment_object' => $comment,
            // 'errors' => $validator->errors(),
        ]);
    }
}
