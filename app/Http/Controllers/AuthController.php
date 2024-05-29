<?php

namespace App\Http\Controllers;
// /** @var \App\Models\User $user **/

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken as AccessToken;

use function PHPUnit\Framework\isNull;

class AuthController extends Controller
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
            $users = User::paginate(3, ['*'], 'page', $page);
        } else {
            $users = User::where(function ($query) use ($searchWord) {
                $query->where('name', 'LIKE', '%' . $searchWord . '%')
                    ->orWhere('email', 'LIKE', '%' . $searchWord . '%')
                    ->orWhere('phone_number', 'LIKE', '%' . $searchWord . '%');
            })
                ->paginate(3, ['*'], 'page', $page);
        }
        // the reason i am using this is to avoid all the extra info that comes with paginate (instead of using a resourse)
        $userData = $users->items(); // Access the user data
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        return response()->json([
            'status' => true,
            'message' => 'data fetched successfully ',
            'users' => $userData,
            // 'errors' => $validator->errors(),
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@          SIGN UP           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function signUpUser(Request $request)
    {
        Log::debug("This Function Is Signup User");
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
                'errors' => Arr::flatten($validator->errors()->toArray()),
                // 'errors' => $validator->errors(),
            ]);
        }
        Log::debug("2");
        $validatedData = $validator->validated();
        Log::debug("3");
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        $user =  User::create([
            "name" => $validatedData["name"],
            "email" => $validatedData["email"],
            "phone_number" => $validatedData["phone_number"],
            "is_blocked" => false,
            "password" => Hash::make($validatedData["password"]),
            "notification_id" => $validatedData["notification_id"],
        ]);
        Log::debug("4");
        Log::debug($validatedData["notification_id"]);
        //
        $token = 'Bearer ' . $user->createToken('User Register')->plainTextToken;
        // $token = $user->createToken('auth_token')->plainTextToken; // Adjust the token name if needed

        Log::debug($token);
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@//
        return response()->json([
            'status' => true,
            'message' => 'user created successfully ',
            'token' => $token,
            'user_object' => $user,
            // 'errors' => $validator->errors(),
        ]);
    }


    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            UPDATE          @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function updateUserLocal(Request $request)
    {
        Log::debug("This Function Is Update Local");
        Log::debug("0");
        $validator = Validator::make($request->all(), [
            "user_id" => "required|integer|exists:users,id",
            'new_local' => 'required|string',
        ]);
        Log::debug("1");

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }

        $validatedData = $validator->validated();

        $user = User::find($validatedData['user_id']);

        $user->update([
            'locale' => $validatedData["new_local"],
        ]);

        Log::debug("3");


        return response()->json([
            'status' => true,
            'message' => 'User info updated successfully',
            // 'user_object' => $user,
        ]);
    }


    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@            UPDATE          @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function updateProfileInfo(Request $request)
    {
        Log::debug("This Function Is Update Profile");
        Log::debug("0");
        $validator = Validator::make($request->all(), [
            "user_id" => "nullable|integer|exists:users,id",
            'name' => 'nullable|string',
            'email' => 'required|string',
            'phone_number' => 'required|string',
            'password' => 'nullable|string|min:6',
        ]);
        Log::debug("1");

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }
        Log::debug("1.5");

        $validatedData = $validator->validated();

        Log::debug("2");
        $user = User::find($validatedData['user_id']);

        $user->update([
            'name' => $validatedData["name"],
            'email' => $validatedData["email"],
            'phone_number' => $validatedData["phone_number"],
            'password' => $validatedData["password"] != null ? $validatedData["password"] : $user->password,
        ]);

        Log::debug("3");
        $token = 'Bearer ' . $user->createToken('User Register')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'User info updated successfully',
            'token' => $token,
            'user_object' => $user,
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@          GET USER          @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//

    public function getUser()
    {
        // $user = auth()->user();

        if (auth()->check()) {
            // User is authenticated
            return response()->json([
                'status' => true,
                'message' => 'user is logged in',
                'user_object' => auth()->user()
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'user is not logged in',
                // 'user_object' => $user
            ]);
        }
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@             GET            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//

    public function getOneUser(Request $request)
    {
        Log::debug("0");
        $validator = Validator::make($request->all(), [
            "user_id" => "required|integer|exists:users,id",
        ]);
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        Log::debug("1");

        $validatedData = $validator->validated();
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        Log::debug("2");
        $user = User::find($validatedData['user_id']);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found',
            ]);
        }
        Log::debug("3");

        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        // i will get the medias from the post
        return response()->json([
            'status' => true,
            'message' => 'Data successfully updated',
            'user_object' => $user, // Pass the object directly
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@           DELETE           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//

    public function deleteUser(Request $request)
    {
        Log::debug("0");
        $validator = Validator::make($request->all(), [
            "user_id" => "required|integer|exists:users,id",
        ]);
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong parameters',
                'errors' => $validator->errors(),
            ]);
        }
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        Log::debug("1");

        $validatedData = $validator->validated();
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        Log::debug("2");
        $user = User::find($validatedData['user_id']);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found',
            ]);
        }
        Log::debug("3");
        $user->forceDelete();
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        //@@@@@@@@@@//
        // i will get the medias from the post
        return response()->json([
            'status' => true,
            'message' => 'user successfully deleted',
            // 'user_object' => $user, // Pass the object directly
        ]);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@           LOG IN           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function loginUser(Request $request)
    {
        Log::debug("This Function Is Login User");
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
        $user = User::where('email', $request->email)->first();

        if ($user) {
            $token = 'Bearer ' . $user->createToken('User Register')->plainTextToken;
            $user->update([
                'notification_id' => $validatedData["notification_id"],
            ]);
            Log::debug($token);
            Log::debug("notification id = " . $validatedData["notification_id"]);
        }
        Log::debug("3");
        //@@@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@@@//
        //@@@@@@@@@@@@@@@//
        if (auth()->attempt([
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
                'user_object' => auth()->user(),
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
        Log::debug("This Function Is Log User Out");
        Log::debug("1");
        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => 'nullable|integer',
            ],
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

        $user = User::where('id', $request->user_id)->first();

        $user->update(['notification_id' => null]);


        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        Log::debug("2");

        return response()->json([
            'status' => true,
            'message' => 'user logged out successfully ',
        ]);
    }


    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@           UPDATE           @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@                            @@@@@@@@@@@@@@@@@@@@@@@@//
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
    public function blockUnblockUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|integer|exists:users,id",
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
        $user = User::find($validatedData['user_id']);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found',
            ]);
        }
        Log::debug("2");


        if ($user) {
            $user->is_blocked = !$user->is_blocked;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Blocked status toggled successfully',
            ]);
        }
    }
}
