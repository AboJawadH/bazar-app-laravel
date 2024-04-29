<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CountryController extends Controller
{


    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function index()
    {
        $countries = Country::All();
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'countries' => $countries,
            // 'errors' => $validator->errors(),
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function fetchActiveCountries()
    {
        $countries = Country::where('is_active', true)->get();
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'countries' => $countries,
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
            'ar_name' => 'required|string',
            'en_name' => 'required|string',
            'tr_name' => 'required|string',
            'flag' => 'required|string',
            'phone_code' => 'nullable|string',
            'country_code' => 'nullable|string',
            'currency' => 'nullable|string',
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


        $country =  Country::create(
            [
                'ar_name' => $validatedData['ar_name'],
                'en_name' => $validatedData['en_name'],
                'tr_name' => $validatedData['tr_name'],
                'flag' => $validatedData['flag'],
                'phone_code' => $validatedData['phone_code'],
                'country_code' => $validatedData['country_code'],
                'currency' => $validatedData['currency'],
                'is_active' => $validatedData['is_active'],
            ],
        );

        return response()->json([
            'status' => true,
            'message' => 'data successfully created',
            'country_object' => $country,
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
        $validator = Validator::make($request->all(), [
            'country_id' => 'required|integer|exists:countries,id',
            'ar_name' => 'nullable|string',
            'en_name' => 'nullable|string',
            'tr_name' => 'nullable|string',
            'flag' => 'nullable|string',
            'phone_code' => 'nullable|string',
            'country_code' => 'nullable|string',
            'currency' => 'nullable|string',
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



        // Find the model instance by ID
        $country = Country::find($validatedData['country_id']);

        if (!$country) {
            return response()->json([
                'status' => false,
                'message' => 'Ad not found',
            ]);
        }

        $country->update(
            [
                'ar_name' => $validatedData['ar_name'],
                'en_name' => $validatedData['en_name'],
                'tr_name' => $validatedData['tr_name'],
                'flag' => $validatedData['flag'],
                'phone_code' => $validatedData['phone_code'],
                'country_code' => $validatedData['country_code'],
                'currency' => $validatedData['currency'],
                'is_active' => $validatedData['is_active'],
            ],
        );

        return response()->json([
            'status' => true,
            'message' => 'data updated successfully ',
            'country_object' => $country,
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



        // Find the model instance by ID
        $country = Country::find($validatedData['country_id']);

        if (!$country) {
            return response()->json([
                'status' => false,
                'message' => 'Ad not found',
            ]);
        }

        $country->delete();

        return response()->json([
            'status' => true,
            'message' => 'data deleted successfully ',
        ]);
    }
}
