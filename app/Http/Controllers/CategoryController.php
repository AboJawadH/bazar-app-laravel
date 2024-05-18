<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function index()
    {
        Log::debug("This function is get all categories");
        Log::debug("0");

        $categories = Category::orderBy("order_number")->get();
        Log::debug("1");
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'categories' => $categories,
            // 'errors' => $validator->errors(),
        ]);
    }
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getActiveCategories()
    {
        Log::debug("This function is get all active categories");
        Log::debug("0");

        $categories = Category::where("is_active", true)->orderBy("order_number")->get();
        Log::debug("1");
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'categories' => $categories,
            // 'errors' => $validator->errors(),
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function getActiveCategoriesForSection(Request $request)
    {
        Log::debug("This function is get categories for section");

        $validator = Validator::make($request->all(), [
            'section_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();

        $query = Category::query();

        $query->where('parent_section_id', $validatedData["section_id"])
        ->where('is_active', true);

        $categories = $query->orderBy('order_number', 'desc')->get();

        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'categories' => $categories,
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
            'ar_name' => 'nullable|string',
            'en_name' => 'nullable|string',
            'tr_name' => 'nullable|string',
            "image" => 'nullable|string',
            "parent_section_name" => 'nullable|string',
            "parent_section_id" => 'nullable|string',
            "order_number" => 'nullable|integer',
            'is_active' => 'boolean',
            'is_main_category' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();


        $category =  Category::create(
            [
                'ar_name' => $validatedData['ar_name'],
                'en_name' => $validatedData['en_name'],
                'tr_name' => $validatedData['tr_name'],
                'image' => $validatedData['image'],
                'parent_section_name' => $validatedData['parent_section_name'],
                'parent_section_id' => $validatedData['parent_section_id'],
                'order_number' => $validatedData['order_number'],
                'is_active' => $validatedData['is_active'],
                'is_main_category' => $validatedData['is_main_category'],
            ],
        );
        return response()->json([
            'status' => true,
            'message' => 'data successfully created',
            'category_object' => $category,
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
            'category_id' => 'required|integer|exists:categories,id',
            'ar_name' => 'nullable|string',
            'en_name' => 'nullable|string',
            'tr_name' => 'nullable|string',
            "image" => 'nullable|string',
            "parent_section_name" => 'nullable|string',
            "parent_section_id" => 'nullable|string',
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
        $category = Category::find($validatedData['category_id']);

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found',
            ]);
        }

        $category->update(
            [
                'ar_name' => $validatedData['ar_name'],
                'en_name' => $validatedData['en_name'],
                'tr_name' => $validatedData['tr_name'],
                'image' => $validatedData['image'],
                'parent_section_name' => $validatedData['parent_section_name'],
                'parent_section_id' => $validatedData['parent_section_id'],
                'order_number' => $validatedData['order_number'],
                'is_active' => $validatedData['is_active'],
                'is_main_category' => $validatedData['is_main_category'],
            ],
        );

        return response()->json([
            'status' => true,
            'message' => 'data updated successfully ',
            'category_object' => $category,
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
            'category_id' => 'required|integer|exists:categories,id',
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
        $category = Category::find($validatedData['category_id']);
        Log::debug("4");

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Ad not found',
            ]);
        }

        Log::debug("5");

        $category->delete();

        Log::debug("6");

        return response()->json([
            'status' => true,
            'message' => 'data deleted successfully ',
        ]);
    }
}
