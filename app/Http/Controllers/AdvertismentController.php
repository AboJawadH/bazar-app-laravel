<?php

namespace App\Http\Controllers;

use App\Models\Advertisment;
use Illuminate\Http\Request;
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
                'errors' => $validator->errors(),
            ]);
        }
        $validatedData = $validator->validated();
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        $page = $request->input('page', 1);
        $searchWord = $validatedData["search_word"];

        if (is_null($validatedData["search_word"])) {
            $ads = Advertisment::orderBy('importance', 'desc')->paginate(3, ['*'], 'page', $page);
        } else {
            $ads = Advertisment::where(function ($query) use ($searchWord) {
                $query->where('title', 'LIKE', '%' . $searchWord . '%');
            })
                ->orderBy('importance', 'desc')->paginate(3, ['*'], 'page', $page);
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
    public function getAdvertisementsByLocation(Request $request)

    {
        Log::debug("This Function Is Fetch Ads Based On Location");
        Log::debug("0");
        //@@@@@@@@@//@@@@@@@@@//
        //@@@@@@@@@//@@@@@@@@@//
        //@@@@@@@@@//@@@@@@@@@//
        $validator = Validator::make($request->all(), [
            'city_id' => 'nullable|integer|exists:cities,id',
            'country_id' => 'nullable|integer|exists:countries,id',
        ]);
        Log::debug($validator->errors());

        Log::debug("1");

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }
        // Log::debug($validator->errors());
        Log::debug("2");

        $validatedData = $validator->validated();
        //@@@@@@@@@//@@@@@@@@@//
        //@@@@@@@@@//@@@@@@@@@//
        //@@@@@@@@@//@@@@@@@@@//
        // general
        // $ads = Advertisment::All();

        $queryGeneral = Advertisment::query()->where("is_general", true)->where('is_active', true);
        $querySpecial = Advertisment::query()->where("is_general", false)->where('is_active', true);
        // country
        if (!is_null($validatedData["country_id"]) && is_null($validatedData["city_id"])) {
            Log::debug("there is country");
            $querySpecial->where('country_id', $validatedData["country_id"])
                ->whereHas("country", function ($query) {
                    $query->where('is_active', true);
                })
                ->where('city_id', null);
        }
        // city
        if ($validatedData["country_id"] !== null && $validatedData["city_id"] !== null) {
            Log::debug("there is country and city");

            $countryId = $validatedData["country_id"];
            $cityId = $validatedData["city_id"];

            $querySpecial->where(function ($query) use ($countryId, $cityId) {
                $query->where(function ($query) use ($countryId, $cityId) {
                    $query->where('country_id', $countryId)
                        ->whereHas("country", function ($query) {
                            $query->where('is_active', true);
                        })
                        ->where('city_id', $cityId)
                        ->whereHas("city", function ($query) {
                            $query->where('is_active', true);
                        });
                })->orWhere(function ($query) use ($countryId) {
                    $query->where('country_id', $countryId)
                        ->whereHas("country", function ($query) {
                            $query->where('is_active', true);
                        })
                        ->whereNull('city_id');
                });
            });
            // $querySpecial
            // ->where('country_id', $validatedData["country_id"])
            //     ->whereHas("country", function ($query) {
            //         $query->where('is_active', true);
            //     })
            //     ->where('city_id', $validatedData["city_id"])
            //     ->whereHas("city", function ($query) {
            //         $query->where('is_active', true);
            //     });
            // ->orWhere('country_id', $validatedData["country_id"])
            // ->orWhereHas("country", function ($query) {
            //     $query->where('is_active', true);
            // });
        }
        $adsGeneral = $queryGeneral->get();
        $adsSpecial = $querySpecial->get();
        $ads = $adsGeneral->merge($adsSpecial); // Merge both collections

        Log::debug("3");

        //@@@@@@@@@//@@@@@@@@@//
        //@@@@@@@@@//@@@@@@@@@//
        //@@@@@@@@@//@@@@@@@@@//
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'ads' => $ads,
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
        Log::debug("This function is add a new ad");
        Log::debug("0");

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string',
            'importance' => 'nullable|string',
            'ad_type' => 'nullable|string',
            'city_id' => 'nullable|integer|exists:cities,id',
            'city_name' => 'nullable|string',
            'country_id' => 'nullable|integer|exists:countries,id',
            'country_name' => 'nullable|string',
            'post_id' => 'nullable|integer',
            'post_title' => 'nullable|string',
            'image' => 'nullable|string',
            'ads_link' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_general' => 'nullable|boolean',
        ]);
        Log::debug($validator->errors());
        Log::debug("1");

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
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

            // $extension = pathinfo($base64Image, "png");

            // Generate a unique filename for the image
            $imageName = time() . '.' . "png";
            // $imageName =  Str::random().'.'.$base64Image->getClientOriginalExtension();
            // Specify the storage path where you want to save the image
            // $storagePath = public_path('storage/ads' . $imageName);
            // $storagePath = storage_path('app/public/ads/' . $imageName);
            // $imageData->storePubliclyAs('ads', $imageName);
            // if (!Storage::disk('public')->exists('ads')) {
            //     return Storage::disk('public')->makeDirectory('ads'); // Create the folder if needed
            // }
            Storage::disk('public')->put('ads/' . $imageName, $imageData);

            // Save the image to the specified path
            // file_put_contents($storagePath, $imageData);
            // $request->file("image_link")->store("ads", "public");
            // $imageUrl = Storage::disk('public')->url('ads/' . $imageName);
            $imageUrl = asset('storage/ads/' . $imageName);
            // $imagePath = "ads/" . $imageName;
        }


        $ad =  Advertisment::create(
            [
                'title' => $validatedData['title'],
                'importance' => $validatedData['importance'],
                'ad_type' => $validatedData['ad_type'],
                'city_id' => $validatedData['city_id'],
                'city_name' => $validatedData['city_name'],
                'country_id' => $validatedData['country_id'],
                'country_name' => $validatedData['country_name'],
                'post_id' => $validatedData['post_id'],
                'post_title' => $validatedData['post_title'],
                'image' => $imageUrl ?? null,
                'ads_link' => $validatedData['ads_link'],
                'is_active' => (bool) $validatedData['is_active'],
                'is_general' => (bool) $validatedData['is_general'],
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
            'ad_type' => 'nullable|string',
            'city_id' => 'nullable|integer|exists:cities,id',
            'city_name' => 'nullable|string',
            'country_id' => 'nullable|integer|exists:countries,id',
            'country_name' => 'nullable|string',
            'post_id' => 'nullable|integer',
            'image' => 'nullable|string',
            'ads_link' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_general' => 'nullable|boolean',
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
                'ad_type' => $validatedData['ad_type'],
                'city_id' => $validatedData['city_id'],
                'city_name' => $validatedData['city_name'],
                'country_id' => $validatedData['country_id'],
                'country_name' => $validatedData['country_name'],
                'post_id' => $validatedData['post_id'],
                'image' => $imageUrl ?? $ad->image,
                'ads_link' => $validatedData['ads_link'],
                'is_active' => (bool) $validatedData['is_active'],
                'is_general' => (bool) $validatedData['is_general'],
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
                'errors' => $validator->errors(),
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
