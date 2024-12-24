<?php

namespace App\Http\Controllers;

use App\Http\Resources\RegionResource;
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
    public function getAllRegions()
    {
        Log::debug("-----------------------------------");
        Log::debug("This function is get all regions");
        Log::debug("-----------------------------------");


        $regions = Region::with('parentRegion')->get();

        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'regions' => RegionResource::collection($regions),
        ]);
    }
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getMainRegions()
    {
        Log::debug("---------------------------------------------");
        Log::debug("This function is get regions without parents");
        Log::debug("---------------------------------------------");


        $regions = Region::with('parentRegion')->whereNull('parent_region_id')->get();

        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'regions' => RegionResource::collection($regions),
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getRegionsForParentRegion(Request $request)
    {
        Log::debug("----------------------------------------------");
        Log::debug("This function is get regions for parent region");
        Log::debug("----------------------------------------------");

        $validator = Validator::make($request->all(), [
            "parent_region_id" => 'required|integer|exists:regions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();

        $regions = Region::where('parent_region_id', $validatedData['parent_region_id'])
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'regions' => $regions,
        ]);
    }
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            CREATE          @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function store(Request $request)
    {
        Log::debug("-------------------------------------");
        Log::debug("This function is create a new region");
        Log::debug("-------------------------------------");

        $validator = Validator::make($request->all(), [
            'parent_region_id' => 'nullable|integer|exists:regions,id',
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
                'parent_region_id' => $validatedData['parent_region_id'],
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
