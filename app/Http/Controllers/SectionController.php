<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SectionController extends Controller
{
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getAllSections()
    {
        Log::debug("-----------------------------------");
        Log::debug("This function is get all sections");
        Log::debug("-----------------------------------");


        $sections = Section::with('parentSection')->get();

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
    public function getMainSections()
    {
        Log::debug("-----------------------------------");
        Log::debug("This function is get main sections");
        Log::debug("-----------------------------------");


        $sections = Section::whereNull('parent_section_id')
            ->orderBy('order_number')
            ->get();
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
    // public function getActiveSections()
    // {
    //     Log::debug("-----------------------------------------");
    //     Log::debug("This function is get all active sections");
    //     Log::debug("-----------------------------------------");

    //     $sections = Section::where("is_active", true)->orderBy("order_number")->get();
    //     Log::debug("1");
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'data fetched successfully ',
    //         'sections' => $sections,
    //     ]);
    // }
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getAllSubsectionsForSection(Request $request)
    {
        Log::debug("----------------------------------------------");
        Log::debug("This function is get subsections for section");
        Log::debug("----------------------------------------------");

        $validator = Validator::make($request->all(), [
            "parent_section_id" => 'required|integer|exists:sections,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => Arr::flatten($validator->errors()->toArray()),
            ]);
        }

        $validatedData = $validator->validated();

        $subsections = Section::where('parent_section_id', $validatedData['parent_section_id'])
            ->orderBy('order_number')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'sections' => $subsections,
        ]);
    }
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            CREATE          @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function store(Request $request)
    {
        Log::debug("------------------------------------");
        Log::debug("This function is store a new section");
        Log::debug("------------------------------------");

        $validator = Validator::make($request->all(), [
            "image" => 'nullable|string',
            'ar_name' => 'required|string',
            'en_name' => 'nullable|string',
            'tr_name' => 'nullable|string',
            'type' => 'required|string',
            "parent_section_id" => 'nullable|integer|exists:sections,id',
            "parent_section_name" => 'nullable|string',
            "order_number" => 'nullable|integer',
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

        if ($request->filled("image")) {
            $base64Image = $request->input('image');
            Log::debug("3");

            // Decode the base64 string into binary image data
            // $imageData = str_replace('data:image/jpeg;base64,', '', $base64Image);
            // $imageData = str_replace(' ', '+', $imageData);
            $imageData = base64_decode($base64Image);


            // Generate a unique filename for the image
            $imageName = Str::uuid() . '.' . "png";

            Storage::disk('public')->put('sections/' . $imageName, $imageData);
            $imageUrl = asset('storage/sections/' . $imageName);
        }

        $section =  Section::create(
            [
                'image' => $imageUrl ?? null,
                'ar_name' => $validatedData['ar_name'],
                'en_name' => $validatedData['en_name'],
                'tr_name' => $validatedData['tr_name'],
                'type' => $validatedData['type'],
                'parent_section_id' => $validatedData['parent_section_id'],
                'parent_section_name' => $validatedData['parent_section_name'],
                'order_number' => $validatedData['order_number'],
                'is_active' => $validatedData['is_active'],
            ],
        );
        return response()->json([
            'status' => true,
            'message' => 'data successfully created',
            'section_object' => $section,
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@           UPDATE           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function update(Request $request)
    {
        Log::debug("-----------------------------------");
        Log::debug("This function is update section");
        Log::debug("-----------------------------------");

        $validator = Validator::make($request->all(), [
            'section_id' => 'required|integer|exists:sections,id',
            "image" => 'nullable|string',
            'ar_name' => 'required|string',
            'en_name' => 'nullable|string',
            'tr_name' => 'nullable|string',
            'type' => 'required|string',
            "parent_section_id" => 'nullable|integer|exists:sections,id',
            "parent_section_name" => 'nullable|string',
            "order_number" => 'nullable|integer',
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

        if ($request->filled("image")) {
            $base64Image = $request->input('image');

            // Decode the base64 string into binary image data
            // $imageData = str_replace('data:image/jpeg;base64,', '', $base64Image);
            // $imageData = str_replace(' ', '+', $imageData);
            $imageData = base64_decode($base64Image);


            // Generate a unique filename for the image
            $imageName = Str::uuid() . '.' . "png";

            Storage::disk('public')->put('sections/' . $imageName, $imageData);
            $imageUrl = asset('storage/sections/' . $imageName);
        }

        // Find the model instance by ID
        $section = Section::find($validatedData['section_id']);

        if (!$section) {
            return response()->json([
                'status' => false,
                'message' => 'section not found',
            ]);
        }

        $section->update(
            [
                'image' => $imageUrl ?? $section->image,
                'ar_name' => $validatedData['ar_name'],
                'en_name' => $validatedData['en_name'],
                'tr_name' => $validatedData['tr_name'],
                'type' => $validatedData['type'],
                // 'parent_section_name' => $validatedData['parent_section_name'],
                // 'parent_section_id' => $validatedData['parent_section_id'],
                'order_number' => $validatedData['order_number'],
                'is_active' => $validatedData['is_active'],
            ],
        );

        return response()->json([
            'status' => true,
            'message' => 'data updated successfully ',
            'section_object' => $section,
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
}
