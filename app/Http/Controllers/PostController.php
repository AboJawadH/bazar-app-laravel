<?php

namespace App\Http\Controllers;

use _Storage;
use App\Http\Resources\ChatResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use App\Models\PostMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Chat;
use App\Models\Section;

use function PHPUnit\Framework\isNull;

class PostController extends Controller
{


    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function index(Request $request)
    {
        Log::debug("------------------------------------");
        Log::debug("This function is grt filtered posts");
        Log::debug("------------------------------------");
        $validator = Validator::make($request->all(), [
            "section_id" => 'nullable|integer|exists:sections,id',
            'region_id' => 'nullable|integer|exists:regions,id',
            //
            "is_car_new" => "nullable|boolean",
            "is_gear_automatic" => "nullable|boolean",
            //
            "is_family" => "nullable|boolean",
            "is_furnutured" => "nullable|boolean",
            "number_of_rooms" => "nullable|int",
            //
            "sort_by" => "required|string|in:highest_price,lowest_price,most_recent,oldest",
            //
            "search_text" => "nullable|string",
            //
            "user_id" => "nullable|integer|exists:users,id",
            //
            "page" => 'nullable|integer',
        ]);
        Log::debug($validator->errors());
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        $sectionIds = [];

        if (!is_null($validatedData['section_id'])) {
            // Get the main section ID
            $sectionIds[] = $validatedData['section_id'];
            Log::debug("Main section ID: " . $validatedData['section_id']);

            // Fetch all nested subsection IDs
            $allSubsectionIds = $this->getAllSubsections($validatedData['section_id']);

            Log::debug("Fetched all subsection IDs recursively: " . json_encode($allSubsectionIds));

            // Merge all subsection IDs with the main section ID
            $sectionIds = array_merge($sectionIds, $allSubsectionIds);
            Log::debug("All section IDs for query: " . json_encode($sectionIds));
        }
        //@@@@@@@@@@//
        //@@@@@@@@@@// BASE QUERY
        //@@@@@@@@@@//

        Log::debug("1");
        $posts = Post::with('medias', "section", "region", "user")
            ->where('is_closed', false)

            ->when(!empty($sectionIds), function ($query) use ($sectionIds) {
                return $query->whereIn('section_id', $sectionIds);
            });

        Log::debug("2");
        $query = $posts
            // REGION
            ->when(!is_null($validatedData["region_id"]), function ($query) use ($validatedData) {
                return $query->where('region_id', $validatedData["region_id"]);
            })
            // SEARCH
            ->when(!is_null($validatedData["search_text"]), function ($query) use ($validatedData) {
                return $query->where('title', 'LIKE', '%' . $validatedData["search_text"] . '%');
            })
            // CAR
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

        // Sorting Logic
        // dont worry about this weird logic for the prices this is only because
        // the price is stored as a string and that was not sorting fine
        $query->when(isset($validatedData['sort_by']), function ($query) use ($validatedData) {
            switch ($validatedData['sort_by']) {
                case 'highest_price':
                    return $query->orderByRaw('CAST(the_price AS DECIMAL(10,2)) DESC');
                case 'lowest_price':
                    return $query->orderByRaw('CAST(the_price AS DECIMAL(10,2)) ASC');
                case 'most_recent':
                    return $query->orderBy('created_at', 'desc');
                case 'oldest':
                    return $query->orderBy('created_at', 'asc');
                default:
                    return $query->orderBy('created_at', 'desc');
            }
        });

        $page = $request->input('page', 1);

        $posts = $query->orderByDesc("created_at")->paginate(20, ['*'], 'page', $page);
        Log::debug("3");
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        $userFavoritePostIds = [];
        Log::debug("Fetching favorites for user ID: " . $validatedData['user_id']);

        if (!is_null($validatedData['user_id'])) {
            Log::debug("Fetching favorites for user ID: " . $validatedData['user_id']);
            $user = User::find($validatedData['user_id']);
            $userFavoritePostIds = $user->favoritePosts()->pluck('post_id')->toArray();
            Log::debug("Fetched favorite post IDs: " . json_encode($userFavoritePostIds));
        }
        Log::debug('User Favorite Post IDs:', $userFavoritePostIds);
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully',
            'posts' => PostResource::collection($posts),
            'userFavoritePosts' => $userFavoritePostIds,
            // 'errors' => $validator->errors(),
        ]);
    }
    private function getAllSubsections($parentId)
    {
        $subsectionIds = Section::where('parent_section_id', $parentId)
            ->pluck('id')
            ->toArray();

        foreach ($subsectionIds as $subsectionId) {
            $subsectionIds = array_merge($subsectionIds, $this->getAllSubsections($subsectionId));
        }

        return $subsectionIds;
    }
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function filterPosts(Request $request)
    {
        Log::debug("---------------------------------------------");
        Log::debug("This function is filter posts for dash board");
        Log::debug("---------------------------------------------");

        $validator = Validator::make($request->all(), [
            "section_id" => 'nullable|integer|exists:sections,id',
            'region_id' => 'nullable|integer|exists:regions,id',
            "search_word" => "nullable|string",
            "page" => 'nullable|integer',
            "isPending" => 'nullable|boolean', // <-- Added this line
        ]);

        Log::debug($validator->errors());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();

        // BASE QUERY
        $posts = Post::with('medias', "user", "region", "section");

        $query = $posts
            // SECTION
            ->when(!is_null($validatedData["section_id"] ?? null), function ($query) use ($validatedData) {
                return $query->where('section_id', $validatedData["section_id"]);
            })
            // REGION
            ->when(!is_null($validatedData["region_id"] ?? null), function ($query) use ($validatedData) {
                return $query->where('region_id', $validatedData["region_id"]);
            })
            // SEARCH
            ->when(!is_null($validatedData["search_word"] ?? null), function ($query) use ($validatedData) {
                return $query->where(function ($q) use ($validatedData) {
                    $q->where('title', 'LIKE', '%' . $validatedData["search_word"] . '%')
                        ->orWhere('id', $validatedData["search_word"]);
                });
            })
            // isPending
            ->when(array_key_exists('isPending', $validatedData), function ($query) use ($validatedData) {
                $status = $validatedData["isPending"] ? 'pending' : 'release';
                return $query->where('status', $status);
            });

        $page = $request->input('page', 1);

        $posts = $query->orderByDesc("created_at")->paginate(10, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully',
            'posts' => PostResource::collection($posts),
        ]);
    }
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getAllPosts(Request $request)
    {

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
            "section_id" => "required|integer|exists:sections,id",
            'region_id' => 'nullable|integer|exists:regions,id',
        ]);
        Log::debug($validator->errors());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();
        //@@@@@@@@@@//
        //@@@@@@@@@@// BASE QUERY
        //@@@@@@@@@@//
        $posts = Post::with('medias')
            ->where('section_id', $validatedData["section_id"]);


        if (is_null($validatedData["region_id"])) {
            $posts = $posts->orderBy('created_at', 'desc')->take(10)->get();
        } else {
            $posts = $posts->orderBy('created_at', 'desc')
                ->where('region_id', $validatedData["region_id"])
                ->take(10)->get();
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
    public function getPendingPosts()
    {
        Log::debug("Fetching Pending Posts");

        //@@@@@@@@@@//
        //@@@@@@@@@@// BASE QUERY
        //@@@@@@@@@@//
        $posts = Post::with('medias')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(30)
            ->get();

        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        return response()->json([
            'status' => true,
            'message' => 'Pending posts fetched successfully',
            'posts' => PostResource::collection($posts),
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
            "user_id" => "nullable|integer|exists:users,id",
        ]);
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
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
        $post = Post::with('medias', "user", "section", "region")->find($validatedData['post_id']);
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found',
            ]);
        }
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        $newFavoriteStatus = false;
        if (!is_null($validatedData['user_id'])) {
            $user = User::find($validatedData['user_id']);
            if ($user->favoritePosts()->where('post_id', $post->id)->exists()) {
                $newFavoriteStatus = true;
                Log::debug("favorite status is : " . $newFavoriteStatus);
            } else {
                $newFavoriteStatus = false;
                Log::debug("favorite status is : " . $newFavoriteStatus);
            }
            // $newFavoriteStatus = $user->favoritePosts()->where('post_id', $post->id)->exists();
        }
        Log::debug("3");
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        // i will get the medias from the post resource
        return response()->json([
            'status' => true,
            'message' => 'Data successfully updated',
            'new_favorite_status' => $newFavoriteStatus,
            'post_object' => new PostResource($post),
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
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        $posts = Post::with('medias', "user", "section", "region",)
            ->where('user_id', $validatedData["user_id"])
            ->orderByDesc("created_at")
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
            'section_id' => 'required|integer|exists:sections,id',
            //
            'region_id' => 'nullable|integer|exists:regions,id',
            'location_text' => 'nullable|string',
            'location_description' => 'nullable|string',
            'longitude' => 'nullable|string',
            'latitude' => 'nullable|string',
            'link' => 'nullable|string',
            //
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'the_price' => 'required|string',
            'currency' => 'required|string',
            'is_active' => 'nullable|boolean',
            //
            'user_id' => 'required|integer|exists:users,id',
            'user_name' => 'required|string',
            'user_phone_number' => 'required|string',
            //
            'is_car_new' => 'nullable|boolean',
            'is_gear_automatic' => 'nullable|boolean',
            'gas_type' => 'nullable|string',
            'car_distanse' => 'nullable|string',
            //
            'is_realestate_for_sale' => 'nullable|boolean',
            'is_realestate_for_family' => 'nullable|boolean',
            'is_realestate_furnitured' => 'nullable|boolean',
            'is_there_elevator' => 'nullable|boolean',
            'number_of_rooms' => 'nullable|integer',
            'number_of_toiltes' => 'nullable|integer',
            'floor_number' => 'nullable|integer',
            //
            'status' => 'nullable|string',
            //
            'medias' => 'nullable|array',
        ]);

        Log::debug($validator->errors());
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();

        $post =  Post::create(
            [
                'section_id' => $validatedData['section_id'],
                //
                'region_id' => $validatedData['region_id'],
                'location_text' => $validatedData['location_text'],
                'location_description' => $validatedData['location_description'],
                'latitude' => $validatedData['latitude'],
                'longitude' => $validatedData['longitude'],
                'link' => $validatedData['link'],
                //
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'the_price' => $validatedData['the_price'],
                'currency' => $validatedData['currency'],
                'is_active' => true,
                'is_special' => false,
                'special_level' => "0",
                'is_favored' => false,
                //
                'user_id' => $validatedData['user_id'],
                'user_name' => $validatedData['user_name'],
                'user_phone_number' => $validatedData['user_phone_number'],
                //
                'is_car_new' => $validatedData['is_car_new'],
                'is_gear_automatic' => $validatedData['is_gear_automatic'],
                'gas_type' => $validatedData['gas_type'],
                'car_distanse' => $validatedData['car_distanse'],
                //
                'is_realestate_for_sale' => $validatedData['is_realestate_for_sale'],
                'is_realestate_for_family' => $validatedData['is_realestate_for_family'],
                'is_realestate_furnitured' => $validatedData['is_realestate_furnitured'],
                'is_there_elevator' => $validatedData['is_there_elevator'],
                'number_of_rooms' => $validatedData['number_of_rooms'],
                'number_of_toiltes' => $validatedData['number_of_toiltes'],
                'floor_number' => $validatedData['floor_number'],
                //
                'status' => $validatedData['status'],
            ],
        );

        if ($request->filled("medias")) {

            $base64Images = $request->input('medias');

            foreach ($base64Images as $base64Image) {
                // Decode the base64 string into binary image data
                $imageData = base64_decode($base64Image);
                // Generate a unique filename for the image
                $imageName = Str::uuid() . '.' . "png";
                // Specify the storage path where you want to save the image
                Storage::disk('public')->put('post-images/' . $imageName, $imageData);
                $imageUrl = asset('storage/post-images/' . $imageName);
                $post->medias()->create([
                    'path' => $imageUrl,
                ]);
            }
            $post->load('medias');
        }

        return response()->json([
            'status' => true,
            'message' => 'data successfully created',
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            UPDATE          @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function update(Request $request)
    {
        Log::debug("This function is edit post");
        Log::debug("0");

        $validator = Validator::make($request->all(), [
            //
            "post_id" => "required|integer|exists:posts,id",
            //
            'section_id' => 'required|integer|exists:sections,id',
            //
            'region_id' => 'nullable|integer|exists:regions,id',
            'location_text' => 'nullable|string',
            'location_description' => 'nullable|string',
            'longitude' => 'nullable|string',
            'latitude' => 'nullable|string',
            'link' => 'nullable|string',
            //
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'the_price' => 'required|string',
            'currency' => 'required|string',
            'is_active' => 'nullable|boolean',
            //
            'user_id' => 'required|integer|exists:users,id',
            'user_name' => 'required|string',
            'user_phone_number' => 'required|string',
            //
            'is_car_new' => 'nullable|boolean',
            'is_gear_automatic' => 'nullable|boolean',
            'gas_type' => 'nullable|string',
            'car_distanse' => 'nullable|string',
            //
            'is_realestate_for_sale' => 'nullable|boolean',
            'is_realestate_for_family' => 'nullable|boolean',
            'is_realestate_furnitured' => 'nullable|boolean',
            'is_there_elevator' => 'nullable|boolean',
            'number_of_rooms' => 'nullable|integer',
            'number_of_toiltes' => 'nullable|integer',
            'floor_number' => 'nullable|integer',
            //
            'status' => 'nullable|string',
            // 'medias' => 'nullable|array',
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
                'section_id' => $validatedData['section_id'],
                //
                'region_id' => $validatedData['region_id'],
                'location_text' => $validatedData['location_text'],
                'location_description' => $validatedData['location_description'],
                'latitude' => $validatedData['latitude'],
                'longitude' => $validatedData['longitude'],
                'link' => $validatedData['link'],
                //
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'the_price' => $validatedData['the_price'],
                'currency' => $validatedData['currency'],
                'is_active' => true,
                'is_special' => $post->is_special,
                'special_level' => "0",
                'is_favored' => false,
                //
                'user_id' => $validatedData['user_id'],
                'user_name' => $validatedData['user_name'],
                'user_phone_number' => $validatedData['user_phone_number'],
                //
                'is_car_new' => $validatedData['is_car_new'],
                'is_gear_automatic' => $validatedData['is_gear_automatic'],
                'gas_type' => $validatedData['gas_type'],
                'car_distanse' => $validatedData['car_distanse'],
                //
                'is_realestate_for_sale' => $validatedData['is_realestate_for_sale'],
                'is_realestate_for_family' => $validatedData['is_realestate_for_family'],
                'is_realestate_furnitured' => $validatedData['is_realestate_furnitured'],
                'is_there_elevator' => $validatedData['is_there_elevator'],
                'number_of_rooms' => $validatedData['number_of_rooms'],
                'number_of_toiltes' => $validatedData['number_of_toiltes'],
                'floor_number' => $validatedData['floor_number'],
                //
                'status' => $validatedData['status'],
            ],
        );

        return response()->json([
            'status' => true,
            'message' => 'data successfully updated',
            // 'post_object' => $post,
            // 'errors' => $validator->errors(),
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
            "post_id" => "required|integer|exists:posts,id",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();

        $post = Post::find($validatedData['post_id']);

        foreach ($post->medias as $media) {
            $oldImagePath = public_path('storage/post-images/' . basename($media->path));
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        $post->delete();

        return response()->json([
            'status' => true,
            'message' => 'Post deleted successfully',
        ]);
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
                'errors' => Arr::flatten($validator->errors()->toArray()),
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
                // $storagePath = storage_path('app/public/post-images/' . $imageName);

                // Save the image to the specified path
                // file_put_contents($storagePath, $imageData);
                Storage::disk('public')->put('post-images/' . $imageName, $imageData);

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
                'errors' => Arr::flatten($validator->errors()->toArray()),
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
            $oldImagePath = public_path('storage/post-images/' . basename($media->path));
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
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
    //@@@@@@@@@@@@@@@@@@@@@@           FAVOR            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function favorPost(Request $request)
    {
        Log::debug("this function is favor/unfavor post");
        Log::debug("0");

        $validator = Validator::make($request->all(), [
            "post_id" => "required|integer|exists:posts,id",
            "user_id" => "required|integer|exists:users,id",
        ]);
        Log::debug("1");
        Log::debug($validator->errors());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();


        // Find the model instance by ID
        $post = Post::find($validatedData['post_id']);
        $user = User::find($validatedData['user_id']);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found',
            ]);
        }
        Log::debug("2");

        if ($post) {
            $newFavoriteStatus = false;

            if ($user->favoritePosts()->where('post_id', $post->id)->exists()) {
                // If the post is already favorited, remove it from favorites
                $user->favoritePosts()->detach($post);
                $newFavoriteStatus = false;
                // $post->is_favored = false;
            } else {
                // If the post is not favorited, add it to favorites
                $user->favoritePosts()->attach($post);
                $newFavoriteStatus = true;
                // $post->is_favored = true;
            }
            // $post->save();
            return response()->json([
                'status' => true,
                'message' => 'Favored status toggled successfully',
                'new_favorite_status' => $newFavoriteStatus,
            ],);
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
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        $user = User::find($validatedData['user_id']);

        $posts = $user->favoritePosts()->with('medias')->get();

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
                'errors' => Arr::flatten($validator->errors()->toArray()),
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
    //@@@@@@@@@@@@@@@@@@@@@@           UPDATE           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function releasePost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "post_id" => "required|integer|exists:posts,id",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();

        $post = Post::find($validatedData['post_id']);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found',
            ]);
        }

        $post->status = 'release';
        $post->save();

        return response()->json([
            'status' => true,
            'message' => 'Post status updated to release successfully',
        ]);
    }
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getSpeacialPosts(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "country_id" => "required|integer|exists:countries,id",
            "city_id" => "nullable|integer|exists:cities,id",

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
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
                'errors' => Arr::flatten($validator->errors()->toArray()),
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
                'errors' => Arr::flatten($validator->errors()->toArray()),
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
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@           UPDATE           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function closePost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "post_id" => "required|integer|exists:posts,id",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
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
            $post->is_closed = !$post->is_closed;
            $post->save();

            return response()->json([
                'status' => true,
                'message' => "تم تغيير حالة الإغلاق بنجاح",
            ]);
        }
    }
}
