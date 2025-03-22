<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Advertisment;
use App\Models\Post;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HomePageController extends Controller
{
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getActiveSections()
    {
        Log::debug("-----------------------------------------");
        Log::debug("This function is get all active sections");
        Log::debug("-----------------------------------------");

        $sections = Section::where("is_active", true)->orderBy("order_number")->get();
        Log::debug("1");
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'sections' => $sections,
        ]);
    }
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getAdsAndPosts(Request $request)
    {
        Log::debug("-----------------------------------------");
        Log::debug("This function is get active ads and posts");
        Log::debug("-----------------------------------------");


        $validator = Validator::make($request->all(), [
            "page" => 'nullable|integer',
            "with_ads" => 'required|boolean',
            "search_text" => "nullable|string",
            'region_id' => 'nullable|integer|exists:regions,id',
            'section_id' => 'nullable|integer|exists:sections,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();
        // ---------------------- //
        // ---------------------- // ads
        // ---------------------- //
        $ads = [];
        if ($validatedData['with_ads']) {
            $adsQuery = Advertisment::query()
                ->where("is_active", true);

            if (!is_null($validatedData['section_id'])) {
                // If section_id is provided, fetch ads for that specific section
                $adsQuery->where('section_id', $validatedData['section_id']);
            } else {
                // If section_id is null, fetch only ads where section_id is null
                $adsQuery->whereNull('section_id');
            }

            // Optionally filter by region_id if provided
            // if (!is_null($validatedData['region_id'])) {
            //     $adsQuery->where('region_id', $validatedData['region_id']);
            // }

            $ads = $adsQuery->get();
        }
        // ---------------------- //
        // ---------------------- // special posts
        // ---------------------- //
        $page = $request->input('page', 1);
        $specialPostsQuery = Post::with('medias', 'section', 'region', 'user')
            ->where('is_active', true)
            ->where('is_special', true)
            ->where('is_closed', false)
            ->where("status","release")
            ->orderByDesc('created_at');

        // Conditional logic for section filter (Special Posts)
        if (!is_null($validatedData['section_id'])) {
            $specialPostsQuery->where('section_id', $validatedData['section_id']);
        }

        $specialPosts = $specialPostsQuery->limit(50)->get();
        // ---------------------- //
        // ---------------------- // recent posts
        // ---------------------- //
        $recentPostsQuery = Post::with('medias', 'section', 'region', 'user')
            ->where('is_active', true)
            ->where('is_special', false)
            ->where('is_closed', false)
            ->where("status","release")
            ->orderByDesc('created_at');

        // Conditional logic for section filter (Recent Posts)
        if (!is_null($validatedData['section_id'])) {
            $recentPostsQuery->where('section_id', $validatedData['section_id']);
        }

        $recentPosts = $recentPostsQuery->paginate(20, ['*'], 'page', $page);

        // ---------------------- //
        // ---------------------- //
        // ---------------------- //
        Log::debug("1");
        return response()->json([
            'status' => true,
            'message' => 'Data fetched successfully',
            'ads' => $ads,
            'special_posts' => PostResource::collection($specialPosts),
            'recent_posts' => PostResource::collection($recentPosts),
        ]);
    }
}
