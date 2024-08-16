<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RegionController extends Controller
{

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getRegionsForCity(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'city_id' => 'required|integer|exists:cities,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();

        $city = City::find($validatedData['city_id']);
        $regions = $city->regions;

        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'regions' => $regions,
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
        Log::debug("This Function is create a new region");
        Log::debug("0");
        $validator = Validator::make($request->all(), [
            'country_id' => 'required|integer|exists:countries,id',
            'city_id' => 'required|integer|exists:cities,id',
            'ar_name' => 'required|string',
            'en_name' => 'required|string',
            'tr_name' => 'required|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();


        $region =  Region::create(
            [
                'country_id' => $validatedData['country_id'],
                'city_id' => $validatedData['city_id'],
                'ar_name' => $validatedData['ar_name'],
                'en_name' => $validatedData['en_name'],
                'tr_name' => $validatedData['tr_name'],
                'is_active' => $validatedData['is_active'],
            ],
        );

        return response()->json([
            'status' => true,
            'message' => 'data successfully created',
            'region_object' => $region,
            // 'errors' => $validator->errors(),
        ]);
    }
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            UPDATE          @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'region_id' => 'required|integer|exists:regions,id',
            'ar_name' => 'nullable|string',
            'en_name' => 'nullable|string',
            'tr_name' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();

        $region = Region::find($validatedData['region_id']);

        if (!$region) {
            return response()->json([
                'status' => false,
                'message' => 'region not found',
            ]);
        }

        $region->update(
            [
                'ar_name' => $validatedData['ar_name'],
                'en_name' => $validatedData['en_name'],
                'tr_name' => $validatedData['tr_name'],
                'is_active' => $validatedData['is_active'],
            ],
        );

        return response()->json([
            'status' => true,
            'message' => 'data successfully updated',
            'region_object' => $region,
            // 'errors' => $validator->errors(),
        ]);
    }
}
