<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getCitiesForCountry(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'country_id' => 'required|integer|exists:countries,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();

        $country = Country::find($validatedData['country_id']);
        $cities = $country->cities;

        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'cities' => $cities,
            // 'errors' => $validator->errors(),
        ]);
    }
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getActiveCitiesForCountry(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'country_id' => 'required|integer|exists:countries,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();

        $country = Country::find($validatedData['country_id']);
        $cities = $country->cities->where('is_active', true);

        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'cities' => $cities,
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
        $validator = Validator::make($request->all(), [
            'country_id' => 'required|integer|exists:countries,id',
            'ar_name' => 'required|string',
            'en_name' => 'required|string',
            'tr_name' => 'required|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();


        $city =  City::create(
            [
                'country_id' => $validatedData['country_id'],
                'ar_name' => $validatedData['ar_name'],
                'en_name' => $validatedData['en_name'],
                'tr_name' => $validatedData['tr_name'],
                'is_active' => $validatedData['is_active'],
            ],
        );

        return response()->json([
            'status' => true,
            'message' => 'data successfully created',
            'city_object' => $city,
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
            'city_id' => 'required|integer|exists:cities,id',
            'ar_name' => 'nullable|string',
            'en_name' => 'nullable|string',
            'tr_name' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();

        $city = City::find($validatedData['city_id']);

        if (!$city) {
            return response()->json([
                'status' => false,
                'message' => 'Ad not found',
            ]);
        }

        $city->update(
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
            'city_object' => $city,
            // 'errors' => $validator->errors(),
        ]);
    }


    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@           DELETE           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required|integer|exists:cities,id',
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
        $city = City::find($validatedData['city_id']);

        if (!$city) {
            return response()->json([
                'status' => false,
                'message' => 'Ad not found',
            ]);
        }

        $city->delete();

        return response()->json([
            'status' => true,
            'message' => 'data deleted successfully ',
        ]);
    }
}
