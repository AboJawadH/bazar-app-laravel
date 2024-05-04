<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdvertismentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubcategoryController;
use App\Models\Comment;
use App\Models\Report;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::post('users/fetch', [AuthController::class, "index"]);
Route::post('user/store', [AuthController::class, "signUpUser"]);
Route::post('user/update', [AuthController::class, "updateProfileInfo"]);
Route::post('user/info', [AuthController::class, "getUser"]);
Route::post('user/fetch', [AuthController::class, "getOneUser"]);
Route::post('user/delete', [AuthController::class, "deleteUser"]);
Route::post('user/login', [AuthController::class, "loginUser"]);
Route::post('user/logout', [AuthController::class, "logout"]);
Route::post('user/block', [AuthController::class, "blockUnblockUser"]);


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::post('admin/store', [AdminController::class, "signUpUser"]);
Route::post('admin/login', [AdminController::class, "loginUser"]);
Route::post('admin/logout', [AdminController::class, "logout"]);



/*
|--------------------------------------------------------------------------
| Advertisment Routes
|--------------------------------------------------------------------------
*/
// Route::resource('advertisments', AdvertismentController::class);

Route::post('advertisments/fetch', [AdvertismentController::class, "index"]);
Route::post('advertisments/fetch-by-location', [AdvertismentController::class, "getAdvertisementsByLocation"]);
Route::post('advertisment/store', [AdvertismentController::class, "store"]);
Route::post('advertisment/update', [AdvertismentController::class, "update"]);
Route::post('advertisment/delete', [AdvertismentController::class, "delete"]);

/*
|--------------------------------------------------------------------------
| Country Routes
|--------------------------------------------------------------------------
*/
Route::post('country/fetch', [CountryController::class, "index"]);
Route::post('country/fetch-active', [CountryController::class, "fetchActiveCountries"]);
Route::post('country/store', [CountryController::class, "store"]);
Route::post('country/update', [CountryController::class, "update"]);
Route::post('country/delete', [CountryController::class, "delete"]);

/*
|--------------------------------------------------------------------------
| city Routes
|--------------------------------------------------------------------------
*/
Route::post('cities/fetch', [CityController::class, "getCitiesForCountry"]);
Route::post('cities/fetch-active', [CityController::class, "getActiveCitiesForCountry"]);
Route::post('city/store', [CityController::class, "store"]);
Route::post('city/update', [CityController::class, "update"]);
Route::post('city/delete', [CityController::class, "delete"]);

/*
|--------------------------------------------------------------------------
| category Routes
|--------------------------------------------------------------------------
*/
Route::post('category/fetch', [CategoryController::class, "index"]);
Route::post('category/fetch-for-section', [CategoryController::class, "getActiveCategoriesForSection"]);
Route::post('category/store', [CategoryController::class, "store"]);
Route::post('category/update', [CategoryController::class, "update"]);
Route::post('category/delete', [CategoryController::class, "delete"]);

/*
|--------------------------------------------------------------------------
| Subcategory Routes
|--------------------------------------------------------------------------
*/
Route::post('subcategory/fetch-all', [SubcategoryController::class, "getAllSubcategoriesForCategory"]);
Route::post('subcategory/fetch', [SubcategoryController::class, "getActiveSubcategoriesForCategory"]);
Route::post('subcategory/store', [SubcategoryController::class, "store"]);
Route::post('subcategory/update', [SubcategoryController::class, "update"]);
Route::post('subcategory/delete', [SubcategoryController::class, "delete"]);

/*
|--------------------------------------------------------------------------
| Posts Routes
|--------------------------------------------------------------------------
*/
Route::post('post/fetch', [PostController::class, "index"]);
Route::post('posts/filter', [PostController::class, "filterPosts"]);
Route::post('post/fetch-posts-for-user', [PostController::class, "getPostsForUser"]);
Route::post('post/fetch-all-posts', [PostController::class, "getAllPosts"]);
Route::post('post/fetch-similar-posts', [PostController::class, "getSimilarPosts"]);
Route::post('post/fetch-one-post', [PostController::class, "getOnePost"]);
Route::post('post/store-new-medias', [PostController::class, "storeMedias"]);
Route::post('post/delete-one-media', [PostController::class, "deleteMedia"]);
Route::post('post/store', [PostController::class, "store"])->middleware(['auth:sanctum']);
Route::post('post/update', [PostController::class, "update"])->middleware(['auth:sanctum']);
Route::post('post/delete', [PostController::class, "delete"])->middleware(['auth:sanctum']);
Route::post('post/favor-post', [PostController::class, "favorPost"])->middleware(['auth:sanctum']);
Route::post('post/fetch-favored-post', [PostController::class, "getFavoredPosts"])->middleware(['auth:sanctum']);
Route::post('post/special-post', [PostController::class, "specialPost"]);
Route::post('post/fetch-special-posts', [PostController::class, "getSpeacialPosts"]);
Route::post('post/fetch-recent-posts', [PostController::class, "getRecentPosts"]);
Route::post('post/block', [PostController::class, "blockPost"]);
/*
|--------------------------------------------------------------------------
| Comments Routes
|--------------------------------------------------------------------------
*/
Route::post('comments/fetch', [CommentController::class, "index"]);
Route::post('comment/store', [CommentController::class, "store"]);

/*
|--------------------------------------------------------------------------
| Ratings Routes
|--------------------------------------------------------------------------
*/
Route::post('ratings/fetch', [RatingController::class, "index"]);
Route::post('rating/store', [RatingController::class, "store"]);

/*
|--------------------------------------------------------------------------
| Reports Routes
|--------------------------------------------------------------------------
*/
Route::post('reports/fetch', [ReportController::class, "index"]);
Route::post('report/store', [ReportController::class, "store"]);

/*
|--------------------------------------------------------------------------
| Notifications Routes
|--------------------------------------------------------------------------
*/
Route::post('notifications/fetch', [NotificationController::class, "index"]);
// Route::post('report/store', [ReportController::class, "store"]);
/*
|--------------------------------------------------------------------------
| DashBoard Routes
|--------------------------------------------------------------------------
*/
Route::post('statistics/fetch', [DashBoardController::class, "getStatistics"]);
/*
|--------------------------------------------------------------------------
| Welcome Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});
