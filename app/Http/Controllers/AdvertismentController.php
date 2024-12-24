<?php

namespace App\Http\Controllers;

use App\Models\Advertisment;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isNull;

class AdvertismentController extends Controller
{

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function index(Request $request)
    {
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        $validator = Validator::make($request->all(), [
            "page" => 'nullable|integer',
            "search_word" => "nullable|string",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }
        $validatedData = $validator->validated();
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        $page = $request->input('page', 1);
        $searchWord = $validatedData["search_word"];

        if (is_null($validatedData["search_word"])) {
            $ads = Advertisment::orderBy('importance', 'desc')->paginate(10, ['*'], 'page', $page);
        } else {
            $ads = Advertisment::where(function ($query) use ($searchWord) {
                $query->where('title', 'LIKE', '%' . $searchWord . '%');
            })
                ->orderBy('importance', 'desc')->paginate(10, ['*'], 'page', $page);
        }
        // the reason i am using this is to avoid all the extra info that comes with paginate (instead of using a resourse)
        $adsData = $ads->items();
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'ads' => $adsData,
            // 'errors' => $validator->errors(),
        ]);
    }
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getAdsForHomePage()
    {
        Log::debug("------------------------------------------");
        Log::debug("This function is get all ads for home page");
        Log::debug("------------------------------------------");

        $ads = Advertisment::whereNull(columns: 'region_id')->where("is_active", true)->get();
        Log::debug("1");
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'ads' => $ads,
        ]);
    }


    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@           CREATE           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function store(Request $request)
    {
        Log::debug("This function is add a new ad");
        Log::debug("0");

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string',
            'importance' => 'nullable|string',
            'region_id' => 'nullable|integer|exists:regions,id',
            'section_id' => 'nullable|integer|exists:sections,id',
            'post_id' => 'nullable|integer',
            'post_title' => 'nullable|string',
            'image' => 'nullable|string',
            'ads_link' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        Log::debug($validator->errors());
        Log::debug("1");

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();
        Log::debug("2");

        if ($request->filled("image")) {
            $base64Image = $request->input('image');
            Log::debug("3");

            // Decode the base64 string into binary image data
            $imageData = str_replace('data:image/jpeg;base64,', '', $base64Image);
            $imageData = str_replace(' ', '+', $imageData);
            $imageData = base64_decode($base64Image);

            $imageName = time() . '.' . "png";

            Storage::disk('public')->put('ads/' . $imageName, $imageData);


            $imageUrl = asset('storage/ads/' . $imageName);
        }


        $ad =  Advertisment::create(
            [
                'title' => $validatedData['title'],
                'importance' => $validatedData['importance'],
                'region_id' => $validatedData['region_id'],
                'section_id' => $validatedData['section_id'],
                'post_id' => $validatedData['post_id'],
                'post_title' => $validatedData['post_title'],
                'image' => $imageUrl ?? null,
                'ads_link' => $validatedData['ads_link'],
                'is_active' => (bool) $validatedData['is_active'],
            ],
        );
        Log::debug("4");

        return response()->json([
            'status' => true,
            'message' => 'data successfully created',
            'ad_object' => $ad,
            // 'errors' => $validator->errors(),
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@           UPDATE           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function update(Request $request)
    {
        Log::debug("this function is update ad");
        Log::debug("0");
        $validator = Validator::make($request->all(), [
            "ad_id" => "required|integer|exists:advertisments,id",
            'title' => 'nullable|string',
            'importance' => 'nullable|string',
            'post_id' => 'nullable|integer',
            'image' => 'nullable|string',
            'ads_link' => 'nullable|string',
            'region_id' => 'nullable|integer|exists:regions,id',
            'section_id' => 'nullable|integer|exists:sections,id',
            'is_active' => 'nullable|boolean',
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
        // Log::debug($validator->errors());
        Log::debug("2");
        $validatedData = $validator->validated();

        $ad = Advertisment::findOrFail($validatedData['ad_id']);

        if ($request->filled("image")) {
            if ($ad->image) {
                $oldImagePath = public_path('storage/ads/' . basename($ad->image));
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $base64Image = $request->input('image');
            Log::debug("3");
            // Decode the base64 string into binary image data
            $imageData = base64_decode($base64Image);
            // Generate a unique filename for the image
            $imageName = time() . '.' . "png";
            // Specify the storage path where you want to save the image
            // Save the image to the specified path
            Storage::disk('public')->put('ads/' . $imageName, $imageData);
            $imageUrl = asset('storage/ads/' . $imageName);
        }
        Log::debug("4");
        // Find the model instance by ID
        if (!$ad) {
            return response()->json([
                'status' => false,
                'message' => 'Ad not found',
            ]);
        }
        Log::debug("5");

        $ad->update(
            [
                'title' => $validatedData['title'],
                'importance' => $validatedData['importance'],
                'post_id' => $validatedData['post_id'],
                'image' => $imageUrl ?? $ad->image,
                'ads_link' => $validatedData['ads_link'],
                'region_id' => $validatedData['region_id'],
                'section_id' => $validatedData['section_id'],
                'is_active' => (bool) $validatedData['is_active'],
            ],
        );
        Log::debug("7");

        return response()->json([
            'status' => true,
            'message' => 'data updated successfully ',
            'ad_object' => $ad,
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
            "ad_id" => "required|integer|exists:advertisments,id",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();

        $ad = Advertisment::find($validatedData['ad_id']);

        if ($ad->image) {
            $oldImagePath = public_path('storage/ads/' . basename($ad->image));
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        $ad->delete();

        return response()->json([
            'status' => true,
            'message' => 'data deleted successfully',
        ]);
    }
}
