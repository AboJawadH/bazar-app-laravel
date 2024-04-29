<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@          SIGN UP           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function signUpUser(Request $request)
    {
        Log::debug("This Function Is Signup Admin");
        Log::debug("0");
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|max:20',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string',
            'password' => 'required|min:6',
            'notification_id' => 'nullable|string',
        ]);
        //

        Log::debug("1");
        Log::debug($validator->errors());
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
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        $admin =  Admin::create([
            "name" => $validatedData["name"],
            "email" => $validatedData["email"],
            "phone_number" => $validatedData["phone_number"],
            "password" => Hash::make($validatedData["password"]),
            "notification_id" => $validatedData["notification_id"],
        ]);
        Log::debug("4");
        Log::debug($validatedData["notification_id"]);
        //
        $token = 'Bearer ' . $admin->createToken('User Register')->plainTextToken;
        // $token = $user->createToken('auth_token')->plainTextToken; // Adjust the token name if needed

        Log::debug($token);
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        return response()->json([
            'status' => true,
            'message' => 'admin created successfully ',
            'token' => $token,
            'user_object' => $admin,
            // 'errors' => $validator->errors(),
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@           LOG IN           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function loginUser(Request $request)
    {
        Log::debug("This Function Is Login Admin");
        Log::debug("0");
        //@@@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@@@//
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required|min:6',
                'notification_id' => 'nullable|string',
            ],
            //  [
            //     'email.required' => 'Please enter your email address.',
            //     'email.email' => 'Please enter a valid email address.',
            //     'password.required' => 'Please enter your password.',
            //     'password.min' => 'Your password must be at least 6 characters.',
            // ],
        );
        Log::debug("1");

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ]);
        }
        Log::debug("2");
        Log::debug($validator->errors());

        $validatedData = $validator->validated();
        //@@@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@@@//
        $admin = Admin::where('email', $request->email)->first();

        if ($admin) {
            $token = 'Bearer ' . $admin->createToken('User Register')->plainTextToken;
            $admin->update([
                'notification_id' => $validatedData["notification_id"],
            ]);
            Log::debug($token);
            Log::debug("notification id = " . $validatedData["notification_id"]);
        }
        Log::debug("3");
        Log::debug($validatedData["email"]);
        Log::debug($validatedData["password"]);
        //@@@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@@@//
        // * the reason i am adding this admin in the auth function is to make it clear
        // * to laravel that i am dealing with the users of the model admin and not of
        // * the model user (the admin type user was defined in the config/auth file)
        if (auth('admin')->attempt([
            'email' => $validatedData["email"],
            'password' => $validatedData["password"],
        ])) {
            Log::debug("4");
            request()->session()->regenerate();
            // auth()->user()->update([
            //     'notification_id' => $validatedData["notification_id"],
            // ]);
            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user_object' => auth('admin')->user(),
            ]);
        } else {
            Log::debug("5");
            return response()->json([
                'status' => false,
                'message' => __("auth.failed"),
            ]);
        }
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@           LOG OUT          @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function logout(Request $request)
    {
        Log::debug("This Function Is Log Admin Out");

        // if ($request->user() && $request->user()->currentAccessToken()) {
        //     Log::debug("0");

        //     $request->user()->currentAccessToken()->delete();
        //     Log::debug("1");

        //     // Perform any additional actions
        //     return response()->json([
        //         'message' => 'Access token deleted successfully.',
        //     ]);
        // } else {
        //     return response()->json([
        //         'message' => 'User not found or access token not available.',
        //     ], 404);
        // }
        // $user = Auth::user();

        // // Check if the user is not null
        // if ($user) {
        //     // Retrieve the current access token for the user
        //     $currentAccessToken = $user->currentAccessToken();

        //     // Check if the current access token exists
        //     if ($currentAccessToken) {
        //         // Delete the current access token
        //         $currentAccessToken->delete();

        //         // Return a success response
        //         return response()->json([
        //             'message' => 'Access token deleted successfully.',
        //         ]);
        //     }}
        // Log::debug("Authenticated user: " . $request->user()->name);
        // $request->user()->currentAccessToken()->delete();

        // auth()->logout();

        // $user = auth()->user();
        // Log::debug($request->user()->tokens());
        // $user->tokens->delete();
        // Log::debug("token deleted sucssesfully");
        // Get the currently authenticated user
        // if ($user) {

        // }


        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return response()->json([
            'status' => true,
            'message' => 'user logged out successfully ',
        ]);
    }

}
