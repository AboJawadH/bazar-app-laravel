<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DashBoardController extends Controller
{


    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getStatistics()
    {
        //@@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@@//
        Log::debug("This Function Is Get Stats");
        Log::debug("0");
        //@@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@@//
        $numberOfUsers = User::count();
        $numberOfPosts = Post::count();
        $numberOfPostsThisMonth = Post::whereMonth('created_at', date('m'))->count();
        $numberOfPostsThisDay = Post::whereDay('created_at', date('d'))->count();
        $numberOfMainSectionPosts = Post::where('parent_section_id',"1")->count();
        $numberOfCarsPosts = Post::where('parent_section_id',"2")->count();
        $numberOfRealEstatesPosts = Post::where('parent_section_id',"3")->count();
        $numberOfGiftsPosts = Post::where('parent_section_id',"4")->count();
        $numberOfJopsPosts = Post::where('parent_section_id',"5")->count();

        Log::debug("1");
        //@@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@@//
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'users_count' => (string) $numberOfUsers,
            'posts_count' => (string) $numberOfPosts,
            'month_posts' => (string) $numberOfPostsThisMonth,
            'day_posts' => (string) $numberOfPostsThisDay,
            'main_posts' => (string) $numberOfMainSectionPosts,
            'cars_posts' => (string) $numberOfCarsPosts,
            'realEstates_posts' => (string) $numberOfRealEstatesPosts,
            'gifts_posts' => (string) $numberOfGiftsPosts,
            'jops_posts' => (string) $numberOfJopsPosts,
        ]);
    }
}
