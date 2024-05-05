<?php

namespace App\Http\Controllers;

use _Storage;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PostController extends Controller
{


    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function index(Request $request)
    {
        Log::debug("This function get posts");
        Log::debug("0");
        $validator = Validator::make($request->all(), [
            "section_id" => "nullable|string",
            "category_id" => "nullable|integer|exists:categories,id",
            "subcategory_id" => "nullable|integer|exists:subcategories,id",
            'city_id' => 'nullable|integer|exists:cities,id',
            //
            "is_car_for_sale" => "nullable|boolean",
            "is_car_new" => "nullable|boolean",
            "is_gear_automatic" => "nullable|boolean",
            //
            "is_family" => "nullable|boolean",
            "is_furnutured" => "nullable|boolean",
            "number_of_rooms" => "nullable|int",
            //
            "search_word" => "nullable|string",
            //
            "page" => 'nullable|integer',

        ]);
        Log::debug($validator->errors());
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();
        //@@@@@@@@@@//
        //@@@@@@@@@@// BASE QUERY
        //@@@@@@@@@@//
        Log::debug("1");
        $posts = Post::with('medias')
            ->where('parent_section_id', $validatedData["section_id"])
            ->where('city_id', $validatedData["city_id"]);

        Log::debug("2");
        $query = $posts
            // CATEGORY
            ->when(!is_null($validatedData["category_id"]), function ($query) use ($validatedData) {
                return $query->where('parent_category_id', $validatedData["category_id"]);
            })
            // SUBCATEGORY
            ->when(!is_null($validatedData["subcategory_id"]), function ($query) use ($validatedData) {
                return $query->where('subcategory_id', $validatedData["subcategory_id"]);
            })
            // SEARCH
            ->when(!is_null($validatedData["search_word"]), function ($query) use ($validatedData) {
                return $query->where('title', 'LIKE', '%' . $validatedData["search_word"] . '%');
            })
            // CAR
            ->when(!is_null($validatedData["is_car_for_sale"]), function ($query) use ($validatedData) {
                return $query->where('is_car_forSale', $validatedData["is_car_for_sale"]);
            })
            ->when(!is_null($validatedData["is_car_new"]), function ($query) use ($validatedData) {
                return $query->where('is_car_new', $validatedData["is_car_new"]);
            })
            ->when(!is_null($validatedData["is_gear_automatic"]), function ($query) use ($validatedData) {
                return $query->where('is_gear_automatic', $validatedData["is_gear_automatic"]);
            })
            // REALESTATE
            ->when(!is_null($validatedData["is_family"]), function ($query) use ($validatedData) {
                return $query->where('is_realestate_for_family', $validatedData["is_family"]);
            })
            ->when(!is_null($validatedData["is_furnutured"]), function ($query) use ($validatedData) {
                return $query->where('is_realestate_furnitured', $validatedData["is_furnutured"]);
            })
            ->when(!is_null($validatedData["number_of_rooms"]), function ($query) use ($validatedData) {
                return $query->where('number_of_rooms', $validatedData["number_of_rooms"]);
            });

        $page = $request->input('page', 1);

        $posts = $query->orderByDesc("created_at")->paginate(3, ['*'], 'page', $page);
        Log::debug("3");

        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully',
            'posts' => PostResource::collection($posts),
            // 'errors' => $validator->errors(),
        ]);
    }
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function filterPosts(Request $request)
    {
        Log::debug("This function is filter posts for dash board");
        Log::debug("0");

        $validator = Validator::make($request->all(), [
            "section_id" => "nullable|string",
            "category_id" => "nullable|integer|exists:categories,id",
            "subcategory_id" => "nullable|integer|exists:subcategories,id",
            //
            "search_word" => "nullable|string",
            //
            "page" => 'nullable|integer',

        ]);
        Log::debug($validator->errors());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();
        //@@@@@@@@@@//
        //@@@@@@@@@@// BASE QUERY
        //@@@@@@@@@@//
        $posts = Post::with('medias');


        $query = $posts
            // SECTION
            ->when(!is_null($validatedData["section_id"]), function ($query) use ($validatedData) {
                return $query->where('parent_section_id', $validatedData["section_id"]);
            })
            // CATEGORY
            ->when(!is_null($validatedData["category_id"]), function ($query) use ($validatedData) {
                return $query->where('parent_category_id', $validatedData["category_id"]);
            })
            // SUBCATEGORY
            ->when(!is_null($validatedData["subcategory_id"]), function ($query) use ($validatedData) {
                return $query->where('subcategory_id', $validatedData["subcategory_id"]);
            })
            // SEARCH
            ->when(!is_null($validatedData["search_word"]), function ($query) use ($validatedData) {
                return $query->where('title', 'LIKE', '%' . $validatedData["search_word"] . '%');
            });


        $page = $request->input('page', 1);

        $posts = $query->orderByDesc("created_at")->paginate(3, ['*'], 'page', $page);
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully',
            'posts' => PostResource::collection($posts),
            // 'errors' => $validator->errors(),
        ]);
    }
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getAllPosts(Request $request)
    {

        // $validator = Validator::make($request->all(), [
        //     "section_id" => "required|string",
        //     "category_id" => "required|integer|exists:categories,id",
        //     "subcategory_id" => "nullable|integer|exists:subcategories,id",
        //     'city_id' => 'required|integer|exists:cities,id',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Wrong parameters',
        //         'errors' => $validator->errors(),
        //     ]);
        // }

        // $validatedData = $validator->validated();
        //@@@@@@@@@@//
        //@@@@@@@@@@// BASE QUERY
        //@@@@@@@@@@//
        Log::debug("This function is get all posts");

        $posts = Post::with('medias')->get();

        // foreach ($posts as $post) {
        //     Log::info('Post created at: ' . $post->created_at->format('Y-m-d H:i:s')); // Formatted timestamp
        // }
        // ->where('parent_section_id', $validatedData["section_id"])
        // ->where('parent_category_id', $validatedData["category_id"])
        // ->where('city_id', $validatedData["city_id"]);


        // if (is_null($validatedData["subcategory_id"])) {
        //     $posts = $posts->orderBy('created_at', 'desc')->take(8)->get();
        // } else {
        //     $posts = $posts->orderBy('created_at', 'desc')->take(8)->where('subcategory_id', $validatedData["subcategory_id"])->get();
        // }
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully',
            'posts' => PostResource::collection($posts),
            // 'errors' => $validator->errors(),
        ]);
    }
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getSimilarPosts(Request $request)
    {
        Log::debug("This Function Is Get Similar Posts");

        $validator = Validator::make($request->all(), [
            "section_id" => "required|string",
            "category_id" => "required|integer|exists:categories,id",
            "subcategory_id" => "nullable|integer|exists:subcategories,id",
            'city_id' => 'required|integer|exists:cities,id',
        ]);
        Log::debug($validator->errors());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();
        //@@@@@@@@@@//
        //@@@@@@@@@@// BASE QUERY
        //@@@@@@@@@@//
        $posts = Post::with('medias')
            ->where('parent_section_id', $validatedData["section_id"])
            ->where('parent_category_id', $validatedData["category_id"])
            ->where('city_id', $validatedData["city_id"]);


        if (is_null($validatedData["subcategory_id"])) {
            $posts = $posts->orderBy('created_at', 'desc')->take(8)->get();
        } else {
            $posts = $posts->orderBy('created_at', 'desc')
                ->where('subcategory_id', $validatedData["subcategory_id"])
                ->take(8)->get();
        }
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully',
            'posts' => PostResource::collection($posts),
            // 'errors' => $validator->errors(),
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@             GET            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//

    public function getOnePost(Request $request)
    {
        Log::debug("This Function Is Get One Post");

        Log::debug("0");
        $validator = Validator::make($request->all(), [
            "post_id" => "required|integer|exists:posts,id",
        ]);
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        Log::debug($validator->errors());

        Log::debug("1");

        $validatedData = $validator->validated();
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        Log::debug("2");
        $post = Post::with('medias', "comments")->find($validatedData['post_id']);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found',
            ]);
        }
        Log::debug("3");

        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        // i will get the medias from the post
        return response()->json([
            'status' => true,
            'message' => 'Data successfully updated',
            'post_object' => new PostResource($post), // Pass the object directly
        ]);
    }
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getPostsForUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "user_id" => "nullable|integer|exists:users,id",

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        $posts = Post::with('medias')
            ->where('user_id', $validatedData["user_id"])
            ->get();
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully',
            'posts' => PostResource::collection($posts),
            // 'errors' => $validator->errors(),
        ]);
    }
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            CREATE          @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function store(Request $request)
    {
        Log::debug("This function is store new post");
        Log::debug("0");

        $validator = Validator::make($request->all(), [
            //
            'parent_section_id' => 'required|string',
            'parent_section_name' => 'required|string',
            'parent_category_id' => 'required|integer|exists:categories,id',
            'parent_category_name' => 'required|string',
            'subcategory_name' => 'nullable|string',
            'subcategory_id' => 'nullable|integer|exists:subcategories,id',
            //
            'city_id' => 'required|integer|exists:cities,id',
            'city_name' => 'required|string',
            'city_ar_name' => 'required|string',
            'city_en_name' => 'required|string',
            'city_tr_name' => 'required|string',
            'country_id' => 'required|integer|exists:countries,id',
            'country_name' => 'required|string',
            //
            'title' => 'required|string',
            'description' => 'required|string',
            'post_type' => 'required|string',
            'the_price' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            //
            'user_id' => 'required|integer|exists:users,id',
            'user_name' => 'required|string',
            'user_phone_number' => 'required|string',
            //
            'is_car_forSale' => 'nullable|boolean',
            'is_car_new' => 'nullable|boolean',
            'is_gear_automatic' => 'nullable|boolean',
            'gas_type' => 'nullable|string',
            'car_distanse' => 'nullable|string',
            //
            'is_realestate_for_sale' => 'nullable|boolean',
            'is_realestate_for_family' => 'nullable|boolean',
            'is_realestate_furnitured' => 'nullable|boolean',
            'is_there_elevator' => 'nullable|boolean',
            'realestate_type' => 'nullable|string',
            'number_of_rooms' => 'nullable|integer',
            'number_of_toiltes' => 'nullable|integer',
            'floor_number' => 'nullable|integer',
            //
            'medias' => 'nullable|array',
        ]);

        Log::debug("1");
        Log::debug($validator->errors());
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

        $post =  Post::create(
            [
                'parent_section_id' => $validatedData['parent_section_id'],
                'parent_section_name' => $validatedData['parent_section_name'],
                'parent_category_id' => $validatedData['parent_category_id'],
                'parent_category_name' => $validatedData['parent_category_name'],
                'subcategory_id' => $validatedData['subcategory_id'],
                'subcategory_name' => $validatedData['subcategory_name'],
                //
                'city_id' => $validatedData['city_id'],
                'city_name' => $validatedData['city_name'],
                'city_ar_name' => $validatedData['city_ar_name'],
                'city_en_name' => $validatedData['city_en_name'],
                'city_tr_name' => $validatedData['city_tr_name'],
                'country_id' => $validatedData['country_id'],
                'country_name' => $validatedData['country_name'],
                //
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'post_type' => $validatedData['post_type'],
                'the_price' => $validatedData['the_price'],
                'images' => null,
                'is_active' => true,
                'is_special' => false,
                'special_level' => "0",
                'is_favored' => false,
                //
                'user_id' => $validatedData['user_id'],
                'user_name' => $validatedData['user_name'],
                'user_phone_number' => $validatedData['user_phone_number'],
                //
                'is_car_forSale' => $validatedData['is_car_forSale'],
                'is_car_new' => $validatedData['is_car_new'],
                'is_gear_automatic' => $validatedData['is_gear_automatic'],
                'gas_type' => $validatedData['gas_type'],
                'car_distanse' => $validatedData['car_distanse'],
                //
                'is_realestate_for_sale' => $validatedData['is_realestate_for_sale'],
                'is_realestate_for_family' => $validatedData['is_realestate_for_family'],
                'is_realestate_furnitured' => $validatedData['is_realestate_furnitured'],
                'is_there_elevator' => $validatedData['is_there_elevator'],
                'realestate_type' => $validatedData['realestate_type'],
                'number_of_rooms' => $validatedData['number_of_rooms'],
                'number_of_toiltes' => $validatedData['number_of_toiltes'],
                'floor_number' => $validatedData['floor_number'],
                //
                // 'search_word' => null,
            ],
        );

        Log::debug("4");
        // Log::debug($post->created_at);


        if ($request->filled("medias")) {

            $base64Images = $request->input('medias');

            foreach ($base64Images as $base64Image) {
                // Decode the base64 string into binary image data
                $imageData = base64_decode($base64Image);

                // Generate a unique filename for the image
                $imageName = Str::uuid() . '.' . "png";

                // Specify the storage path where you want to save the image
                // $storagePath = storage_path('app/public/post-images/' . $imageName);

                // Save the image to the specified path
                // file_put_contents($storagePath, $imageData);
                Storage::disk('public')->put('post-images/' . $imageName, $imageData);

                $imageUrl = asset('storage/post-images/' . $imageName);

                $post->medias()->create([
                    'path' => $imageUrl,
                ]);

                // Get the URL of the saved image
                // $imageUrl = asset('storage/post-images/' . $imageName);

                // Store the image URL in an array
                // $imageUrls[] = $imageUrl;

            }

            Log::debug("5");

            $post->load('medias');

            Log::debug("6");

            return response()->json([
                'status' => true,
                'message' => 'data successfully created',
                // 'post_object' => $post,
                // 'errors' => $validator->errors(),
            ]);


            // for ($i = 0; $i < count($request->medias); $i++) {

            //     $imageData = base64_decode($base64Image);

            //     // Generate a unique filename for the image
            //     $imageName = time() . '.' . "png";

            //     // Specify the storage path where you want to save the image
            //     $storagePath = storage_path('app/public/post-images/' . $imageName);

            //     // Save the image to the specified path
            //     file_put_contents($storagePath, $imageData);

            //     $post->medias()->create([
            //         'path' => _Storage::api_upload("medias.$i", 'posts'),
            //     ]);
            // }

        }
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            UPDATE          @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function update(Request $request)
    {
        Log::debug("0");

        $validator = Validator::make($request->all(), [
            //
            "post_id" => "required|integer|exists:posts,id",
            'parent_section_id' => 'required|string',
            'parent_section_name' => 'required|string',
            'parent_category_id' => 'required|integer|exists:categories,id',
            'parent_category_name' => 'required|string',
            'subcategory_name' => 'nullable|string',
            'subcategory_id' => 'nullable|integer|exists:subcategories,id',
            //
            'city_id' => 'required|integer|exists:cities,id',
            'city_name' => 'required|string',
            'city_ar_name' => 'required|string',
            'city_en_name' => 'required|string',
            'city_tr_name' => 'required|string',
            'country_id' => 'required|integer|exists:countries,id',
            'country_name' => 'required|string',
            //
            'title' => 'required|string',
            'description' => 'required|string',
            'post_type' => 'required|string',
            'the_price' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            //
            'user_id' => 'required|integer|exists:users,id',
            'user_name' => 'required|string',
            'user_phone_number' => 'required|string',
            //
            'is_car_forSale' => 'nullable|boolean',
            'is_car_new' => 'nullable|boolean',
            'is_gear_automatic' => 'nullable|boolean',
            'gas_type' => 'nullable|string',
            'car_distanse' => 'nullable|string',
            //
            'is_realestate_for_sale' => 'nullable|boolean',
            'is_realestate_for_family' => 'nullable|boolean',
            'is_realestate_furnitured' => 'nullable|boolean',
            'is_there_elevator' => 'nullable|boolean',
            'realestate_type' => 'nullable|string',
            'number_of_rooms' => 'nullable|integer',
            'number_of_toiltes' => 'nullable|integer',
            'floor_number' => 'nullable|integer',
            //
            // 'medias' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }
        Log::debug("1");

        $validatedData = $validator->validated();
        Log::debug("2");
        // Find the model instance by ID
        $post = Post::find($validatedData['post_id']);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found',
            ]);
        }

        Log::debug("3");

        $post->update(
            [
                'parent_section_id' => $validatedData['parent_section_id'],
                'parent_section_name' => $validatedData['parent_section_name'],
                'parent_category_id' => $validatedData['parent_category_id'],
                'parent_category_name' => $validatedData['parent_category_name'],
                'subcategory_id' => $validatedData['subcategory_id'],
                'subcategory_name' => $validatedData['subcategory_name'],
                //
                'city_id' => $validatedData['city_id'],
                'city_name' => $validatedData['city_name'],
                'city_ar_name' => $validatedData['city_ar_name'],
                'city_en_name' => $validatedData['city_en_name'],
                'city_tr_name' => $validatedData['city_tr_name'],
                'country_id' => $validatedData['country_id'],
                'country_name' => $validatedData['country_name'],
                //
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'post_type' => $validatedData['post_type'],
                'the_price' => $validatedData['the_price'],
                'images' => null,
                'is_active' => true,
                'is_special' => false,
                'special_level' => "0",
                'is_favored' => false,
                //
                'user_id' => $validatedData['user_id'],
                'user_name' => $validatedData['user_name'],
                'user_phone_number' => $validatedData['user_phone_number'],
                //
                'is_car_forSale' => $validatedData['is_car_forSale'],
                'is_car_new' => $validatedData['is_car_new'],
                'is_gear_automatic' => $validatedData['is_gear_automatic'],
                'gas_type' => $validatedData['gas_type'],
                'car_distanse' => $validatedData['car_distanse'],
                //
                'is_realestate_for_sale' => $validatedData['is_realestate_for_sale'],
                'is_realestate_for_family' => $validatedData['is_realestate_for_family'],
                'is_realestate_furnitured' => $validatedData['is_realestate_furnitured'],
                'is_there_elevator' => $validatedData['is_there_elevator'],
                'realestate_type' => $validatedData['realestate_type'],
                'number_of_rooms' => $validatedData['number_of_rooms'],
                'number_of_toiltes' => $validatedData['number_of_toiltes'],
                'floor_number' => $validatedData['floor_number'],
                //
                // 'search_word' => null,
            ],
        );

        Log::debug("4");


        // if ($request->filled("medias")) {

        //     $base64Images = $request->input('medias');

        //     foreach ($base64Images as $base64Image) {
        //         // Decode the base64 string into binary image data
        //         $imageData = base64_decode($base64Image);

        //         // Generate a unique filename for the image
        //         $imageName = Str::uuid() . '.' . "png";

        //         // Specify the storage path where you want to save the image
        //         $storagePath = storage_path('app/public/post-images/' . $imageName);

        //         // Save the image to the specified path
        //         file_put_contents($storagePath, $imageData);

        //         $post->medias()->update([
        //             'path' => $storagePath,
        //         ]);
        //         // Get the URL of the saved image
        //         // $imageUrl = asset('storage/post-images/' . $imageName);

        //         // Store the image URL in an array
        //         // $imageUrls[] = $imageUrl;
        //     }
        // }

        // Log::debug("5");

        // $post->load('medias');

        Log::debug("6");

        return response()->json([
            'status' => true,
            'message' => 'data successfully updated',
            // 'post_object' => $post,
            // 'errors' => $validator->errors(),
        ]);


        // for ($i = 0; $i < count($request->medias); $i++) {

        //     $imageData = base64_decode($base64Image);

        //     // Generate a unique filename for the image
        //     $imageName = time() . '.' . "png";

        //     // Specify the storage path where you want to save the image
        //     $storagePath = storage_path('app/public/post-images/' . $imageName);

        //     // Save the image to the specified path
        //     file_put_contents($storagePath, $imageData);

        //     $post->medias()->create([
        //         'path' => _Storage::api_upload("medias.$i", 'posts'),
        //     ]);
        // }

    }



    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            DELETE          @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "post_id" => "required|integer|exists:posts,id",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();

        $post = Post::find($validatedData['post_id']);

        $post->delete();

        return response()->json([
            'status' => true,
            'message' => 'Post deleted successfully',
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@           DELETE           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function deleteMedia(Request $request)
    {
        Log::debug("0");
        $validator = Validator::make($request->all(), [
            "post_id" => "required|integer|exists:posts,id",
            "media_id" => "required|integer|exists:post_media,id",
        ]);
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        Log::debug("1");

        $validatedData = $validator->validated();
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        Log::debug("2");

        $post = Post::findOrFail($validatedData["post_id"]);
        //
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found',
            ]);
        }
        //
        //
        $media = PostMedia::findOrFail($validatedData["media_id"]);
        //
        if (!$media) {
            return response()->json([
                'status' => false,
                'message' => 'Media not found',
            ]);
        }

        Log::debug("3");

        if ($post->medias->contains($media)) {
            $media->delete();
            // Optionally, you can also remove the media from the post's relationship
            // $post->medias()->detach($media);
            return response()->json([
                'status' => true,
                'message' => 'Media deleted successfully'
            ]);
        }
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        Log::debug("4");

        return response()->json(['message' => 'Media not found for the given post'], 404);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@        STORE MEDIAS        @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function storeMedias(Request $request)
    {
        Log::debug("This Function Is Store New Medias");
        Log::debug("0");
        $validator = Validator::make($request->all(), [
            "post_id" => "required|integer|exists:posts,id",
            'medias' => 'nullable|array',
        ]);
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        Log::debug("1");
        Log::debug($validator->errors());


        $validatedData = $validator->validated();
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        Log::debug("2");

        $post = Post::findOrFail($validatedData["post_id"]);
        //
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found',
            ]);
        }
        Log::debug("3");
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        if ($request->filled("medias")) {

            $base64Images = $request->input('medias');

            foreach ($base64Images as $base64Image) {
                // Decode the base64 string into binary image data
                $imageData = base64_decode($base64Image);

                // Generate a unique filename for the image
                $imageName = Str::uuid() . '.' . "png";

                // Specify the storage path where you want to save the image
                $storagePath = storage_path('app/public/post-images/' . $imageName);

                // Save the image to the specified path
                file_put_contents($storagePath, $imageData);

                $imageUrl = asset('storage/post-images/' . $imageName);

                $post->medias()->create([
                    'path' => $imageUrl,
                ]);
            }
            Log::debug("4");

            //@@@@@@@@@@//
            //@@@@@@@@@@//
            //@@@@@@@@@@//
            return response()->json([
                'status' => true,
                'message' => 'Medias added successfully'
            ]);
        }
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@           UPDATE           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function favorPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "post_id" => "required|integer|exists:posts,id",

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();


        // Find the model instance by ID
        $post = Post::find($validatedData['post_id']);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found',
            ]);
        }
        Log::debug("2");


        if ($post) {
            $post->is_favored = !$post->is_favored;
            $post->save();

            return response()->json([
                'status' => true,
                'message' => 'Favored status toggled successfully',
            ]);
        }
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getFavoredPosts(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "user_id" => "nullable|integer|exists:users,id",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        $posts = Post::with('medias')
            ->where('user_id', $validatedData["user_id"])
            ->where('is_favored', true)
            ->get();
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully',
            'posts' => PostResource::collection($posts),
            // 'errors' => $validator->errors(),
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@           UPDATE           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function specialPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "post_id" => "required|integer|exists:posts,id",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();


        // Find the model instance by ID
        $post = Post::find($validatedData['post_id']);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found',
            ]);
        }
        Log::debug("2");


        if ($post) {
            $post->is_special = !$post->is_special;
            $post->save();

            return response()->json([
                'status' => true,
                'message' => 'Special status toggled successfully',
            ]);
        }
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getSpeacialPosts(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "country_id" => "requried|integer|exists:countries,id",
            "city_id" => "nullable|integer|exists:cities,id",

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        if (!is_null($validatedData["city_id"])) {
            $posts = Post::with('medias')
                ->where('city_id', $validatedData["city_id"])
                ->where('is_special', true)->orderByDesc("created_at")
                ->get();
        } else {
            $posts = Post::with('medias')
                ->where('country_id', $validatedData["country_id"])
                ->where('is_special', true)->orderByDesc("created_at")
                ->get();
        }


        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully',
            'posts' => PostResource::collection($posts),
            // 'errors' => $validator->errors(),
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getRecentPosts(Request $request)
    {
        Log::debug("0");

        $validator = Validator::make($request->all(), [
            "country_id" => "required|integer|exists:countries,id",
            "city_id" => "nullable|integer|exists:cities,id",
        ]);
        Log::debug("1");

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }
        Log::debug($validator->errors());

        Log::debug("2");

        $validatedData = $validator->validated();
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        if (!is_null($validatedData["city_id"])) {
            $posts = Post::with('medias')
                ->where('city_id', $validatedData["city_id"])
                ->orderByDesc("created_at")->limit(10)
                ->get();
        } else {
            $posts = Post::with('medias')
                ->where('country_id', $validatedData["country_id"])
                ->orderByDesc("created_at")->limit(10)
                ->get();
        }

        Log::debug("3");
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully',
            'posts' => PostResource::collection($posts),
            // 'errors' => $validator->errors(),
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@           UPDATE           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function blockPost(Request $request)
    {
        Log::debug("This function is activate / deactivate post ");

        $validator = Validator::make($request->all(), [
            "post_id" => "required|integer|exists:posts,id",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();


        // Find the model instance by ID
        $post = Post::find($validatedData['post_id']);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found',
            ]);
        }
        Log::debug("2");


        if ($post) {
            $post->is_active = !$post->is_active;
            $post->save();

            return response()->json([
                'status' => true,
                'message' => 'Active status toggled successfully',
            ]);
        }
    }
}
