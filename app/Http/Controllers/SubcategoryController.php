<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SubcategoryController extends Controller
{

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//

    public function getActiveSubCategories()
    {
        Log::debug("This function is get all active subcategories");
        Log::debug("0");

        $subcategories = Subcategory::where("is_active", true)->orderBy("order_number")->get();
        Log::debug("1");
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'subcategories' => $subcategories,
            // 'errors' => $validator->errors(),
        ]);
    }


    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getAllSubcategoriesForCategory(Request $request)
    {
        Log::debug("This function is get subcategories for category");

        $validator = Validator::make($request->all(), [
            "category_id" => 'required|integer|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();

        $category = Category::find($validatedData['category_id']);

        $subcategories = $category->subcategories()
            ->orderBy("order_number")->get();

        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'subcategories' => $subcategories,
            // 'errors' => $validator->errors(),
        ]);
    }
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getActiveSubcategoriesForCategory(Request $request)
    {
        Log::debug("This function is get subcategories for category");

        $validator = Validator::make($request->all(), [
            "category_id" => 'required|integer|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();

        $category = Category::find($validatedData['category_id']);

        $subcategories = $category->subcategories()
            ->where('is_active', true)
            ->orderBy("order_number")->get();

        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'subcategories' => $subcategories,
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
            "image" => 'nullable|string',
            "parent_section_name" => 'required|string',
            "parent_section_id" => 'required|string',
            "category_id" => 'required|integer|exists:categories,id',
            "category_name" => 'required|string',
            "order_number" => 'nullable|integer',
            'is_active' => 'boolean',
            'is_main_category' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();


        $subcategory =  Subcategory::create(
            [
                'ar_name' => $validatedData['ar_name'],
                'en_name' => $validatedData['en_name'],
                'tr_name' => $validatedData['tr_name'],
                'image' => $validatedData['image'],
                'parent_section_name' => $validatedData['parent_section_name'],
                'parent_section_id' => $validatedData['parent_section_id'],
                'category_id' => $validatedData['category_id'],
                'category_name' => $validatedData['category_name'],
                'order_number' => $validatedData['order_number'],
                'is_active' => $validatedData['is_active'],
                'is_main_category' => $validatedData['is_main_category'],
            ],
        );

        return response()->json([
            'status' => true,
            'message' => 'data successfully created',
            'subcategory_object' => $subcategory,
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
            'subcategory_id' => 'required|integer|exists:subcategories,id',
            'ar_name' => 'nullable|string',
            'en_name' => 'nullable|string',
            'tr_name' => 'nullable|string',
            "image" => 'nullable|string',
            "parent_section_name" => 'nullable|string',
            "parent_section_id" => 'nullable|string',
            "category_id" => 'required|integer|exists:categories,id',
            "category_name" => 'required|string',
            "order_number" => 'nullable|integer',
            'is_active' => 'boolean',
            'is_main_category' => 'boolean',
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
        $subcategory = Subcategory::find($validatedData['subcategory_id']);

        if (!$subcategory) {
            return response()->json([
                'status' => false,
                'message' => 'Subcategory not found',
            ]);
        }

        $subcategory->update(
            [
                'ar_name' => $validatedData['ar_name'],
                'en_name' => $validatedData['en_name'],
                'tr_name' => $validatedData['tr_name'],
                'image' => $validatedData['image'],
                'parent_section_name' => $validatedData['parent_section_name'],
                'parent_section_id' => $validatedData['parent_section_id'],
                'category_id' => $validatedData['category_id'],
                'category_name' => $validatedData['category_name'],
                'order_number' => $validatedData['order_number'],
                'is_active' => $validatedData['is_active'],
                'is_main_category' => $validatedData['is_main_category'],
            ],
        );

        return response()->json([
            'status' => true,
            'message' => 'data updated successfully ',
            'subcategory_object' => $subcategory,
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
        Log::debug("0");

        $validator = Validator::make($request->all(), [
            'subcategory_id' => 'required|integer|exists:subcategories,id',
        ]);
        Log::debug("1");

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


        // Find the model instance by ID
        $subcategory = Subcategory::find($validatedData['subcategory_id']);
        Log::debug("4");

        if (!$subcategory) {
            return response()->json([
                'status' => false,
                'message' => 'Ad not found',
            ]);
        }

        Log::debug("5");

        $subcategory->delete();

        Log::debug("6");

        return response()->json([
            'status' => true,
            'message' => 'data deleted successfully ',
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
}
