<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{


    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            FETCH           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function index(Request $request)
    {
        Log::debug("This function is get all reports");

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
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        $page = $request->input('page', 1);
        $searchWord = $validatedData["search_word"];
        if (is_null($validatedData["search_word"])) {
            $reports = Report::paginate(3, ['*'], 'page', $page);
        } else {
            $reports = Report::where(function ($query) use ($searchWord) {
                $query->where('post_title', 'LIKE', '%' . $searchWord . '%')
                    ->orWhere('reporter_name', 'LIKE', '%' . $searchWord . '%')
                    ->orWhere('post_publisher_name', 'LIKE', '%' . $searchWord . '%')
                    ->orWhere('report_message', 'LIKE', '%' . $searchWord . '%');
            })
                ->paginate(3, ['*'], 'page', $page);
        }
        // the reason i am using this is to avoid all the extra info that comes with paginate (instead of using a resourse)
        $reportsData = $reports->items();

        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully',
            'reports' => $reportsData,
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
        Log::debug("this function is send new report");
        Log::debug("0");

        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer|exists:posts,id',
            'post_publisher_id' => 'required|integer|exists:users,id',
            'post_publisher_name' => 'required|string',
            'post_title' => 'required|string',
            //
            'rporter_id' => 'required|integer|exists:users,id',
            'reporter_name' => 'required|string',
            'report_title' => 'required|string',
            'report_message' => 'required|string',
        ]);
        Log::debug($validator->errors());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();
        Log::debug("1");

        $report =  Report::create(
            [
                'post_id' => $validatedData['post_id'],
                'post_publisher_id' => $validatedData['post_publisher_id'],
                'post_publisher_name' => $validatedData['post_publisher_name'],
                'post_title' => $validatedData['post_title'],
                //
                'rporter_id' => $validatedData['rporter_id'],
                'reporter_name' => $validatedData['reporter_name'],
                'report_title' => $validatedData['report_title'],
                'report_message' => $validatedData['report_message'],

            ],

        );
        Log::debug("2");

        return response()->json([
            'status' => true,
            'message' => 'data successfully created',
            // 'report_object' => $report,

            // 'errors' => $validator->errors(),
        ]);
    }
}
