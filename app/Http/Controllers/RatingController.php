<?php

namespace App\Http\Controllers;

use App\Http\Resources\RatingResource;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Post;
use App\Models\User;
use App\Notifications\RatingCreatedNotification;
use Illuminate\Support\Arr;

class RatingController extends Controller
{


    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function index(Request $request)
    {
        //@@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@@//
        Log::debug("0");
        $validator = Validator::make($request->all(), [
            "post_id" => 'required|integer|exists:posts,id',
            "page" => 'nullable|integer',
        ]);
        Log::debug("1");
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }
        Log::debug("2");

        $validatedData = $validator->validated();
        Log::debug("3");
        //@@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@@//
        $page = $request->input('page', 1);
        $ratings = Rating::with('user',)->where("post_id", $validatedData["post_id"])->orderByDesc("created_at")->paginate(5, ['*'], 'page', $page);
        // the name of the column you want the average for
        $avg = Rating::where("post_id", $validatedData["post_id"])->avg("rating_value");
        $avgRatingString = number_format($avg, 1, '.', ''); // Format to 2 decimal places without trailing zeroes
        $count = Rating::where("post_id", $validatedData["post_id"])->count();
        Log::debug("4");
        //@@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@@//
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'ratings' => RatingResource::collection($ratings),
            'avg' => $avgRatingString,
            'count' => (string) $count,
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
        Log::debug("this function is store a new rating");
        Log::debug("0");

        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string',
            'post_id' => 'required|integer|exists:posts,id',
            'user_id' => 'required|integer|exists:users,id',
            'rating_review' => 'required|string',
            'rating_value' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();
        Log::debug("1");


        Log::debug("2");

        $rating =  Rating::create(
            [
                'rating_review' => $validatedData['rating_review'],
                'rating_value' => $validatedData['rating_value'],
                'user_name' => $validatedData['user_name'],
                'post_id' => $validatedData['post_id'],
                'user_id' => $validatedData['user_id'],
            ],
        );

        $post = Post::findOrFail($rating->post_id); // Assuming you have the post ID
        $postPublisher = User::findOrFail($post->user_id); // Assuming you have the post ID
        Log::debug("4");
        $postPublisher->notify(new RatingCreatedNotification($rating, $post));
        Log::debug("5");

        return response()->json([
            'status' => true,
            'message' => 'data successfully created',
            // 'rating_object' => $rating,

            // 'errors' => $validator->errors(),
        ]);
    }
}
