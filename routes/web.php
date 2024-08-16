<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdvertismentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RegionController;
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
Route::post('user/local-update', [AuthController::class, "updateUserLocal"])->middleware(['auth:sanctum']);
Route::post('user/update', [AuthController::class, "updateProfileInfo"])->middleware(['auth:sanctum']);
Route::post('user/info', [AuthController::class, "getUser"]);
Route::post('user/fetch', [AuthController::class, "getOneUser"]);
Route::post('user/delete', [AuthController::class, "deleteUser"])->middleware(['auth:sanctum-admin']);
Route::post('user/login', [AuthController::class, "loginUser"]);
Route::post('user/logout', [AuthController::class, "logout"]);
Route::post('user/block', [AuthController::class, "blockUnblockUser"])->middleware(['auth:sanctum-admin']);


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
Route::post('advertisment/store', [AdvertismentController::class, "store"])->middleware(['auth:sanctum-admin']);
Route::post('advertisment/update', [AdvertismentController::class, "update"])->middleware(['auth:sanctum-admin']);
Route::post('advertisment/delete', [AdvertismentController::class, "delete"])->middleware(['auth:sanctum-admin']);

/*
|--------------------------------------------------------------------------
| Country Routes
|--------------------------------------------------------------------------
*/
Route::post('country/fetch', [CountryController::class, "index"]);
Route::post('country/fetch-active', [CountryController::class, "fetchActiveCountries"]);
Route::post('country/store', [CountryController::class, "store"])->middleware(['auth:sanctum-admin']);
Route::post('country/update', [CountryController::class, "update"])->middleware(['auth:sanctum-admin']);
Route::post('country/delete', [CountryController::class, "delete"])->middleware(['auth:sanctum-admin']);

/*
|--------------------------------------------------------------------------
| city Routes
|--------------------------------------------------------------------------
*/
Route::post('cities/fetch', [CityController::class, "getCitiesForCountry"]);
Route::post('cities/fetch-active-only', [CityController::class, "fetchActiveCities"]);
Route::post('cities/fetch-active', [CityController::class, "getActiveCitiesForCountry"]);
Route::post('city/store', [CityController::class, "store"])->middleware(['auth:sanctum-admin']);
Route::post('city/update', [CityController::class, "update"])->middleware(['auth:sanctum-admin']);
Route::post('city/delete', [CityController::class, "delete"])->middleware(['auth:sanctum-admin']);

/*
|--------------------------------------------------------------------------
| region Routes
|--------------------------------------------------------------------------
*/
Route::post('regions/fetch', [RegionController::class, "getRegionsForCity"]);
Route::post('region/store', [RegionController::class, "store"]);
Route::post('region/update', [RegionController::class, "update"]);






/*
|--------------------------------------------------------------------------
| category Routes
|--------------------------------------------------------------------------
*/
Route::post('category/fetch', [CategoryController::class, "index"]);
Route::post('category/active-fetch', [CategoryController::class, "getActiveCategories"]);
Route::post('category/fetch-for-section', [CategoryController::class, "getActiveCategoriesForSection"]);
Route::post('category/store', [CategoryController::class, "store"])->middleware(['auth:sanctum-admin']);
Route::post('category/update', [CategoryController::class, "update"])->middleware(['auth:sanctum-admin']);
Route::post('category/delete', [CategoryController::class, "delete"])->middleware(['auth:sanctum-admin']);

/*
|--------------------------------------------------------------------------
| Subcategory Routes
|--------------------------------------------------------------------------
*/
Route::post('subcategory/fetch-all', [SubcategoryController::class, "getAllSubcategoriesForCategory"]);
Route::post('subcategory/active-fetch', [SubcategoryController::class, "getActiveSubCategories"]);
Route::post('subcategory/fetch', [SubcategoryController::class, "getActiveSubcategoriesForCategory"]);
Route::post('subcategory/store', [SubcategoryController::class, "store"])->middleware(['auth:sanctum-admin']);
Route::post('subcategory/update', [SubcategoryController::class, "update"])->middleware(['auth:sanctum-admin']);
Route::post('subcategory/delete', [SubcategoryController::class, "delete"])->middleware(['auth:sanctum-admin']);

/*
|--------------------------------------------------------------------------
| Posts Routes
|--------------------------------------------------------------------------
*/
Route::post('post/fetch', [PostController::class, "index"]);
Route::post('posts/filter', [PostController::class, "filterPosts"]);
Route::post('post/fetch-posts-for-user', [PostController::class, "getPostsForUser"])->middleware(['user-or-admin']);
Route::post('post/fetch-all-posts', [PostController::class, "getAllPosts"]);
Route::post('post/fetch-similar-posts', [PostController::class, "getSimilarPosts"]);
Route::post('post/fetch-one-post', [PostController::class, "getOnePost"]);
Route::post('post/store-new-medias', [PostController::class, "storeMedias"])->middleware(['auth:sanctum']);
Route::post('post/delete-one-media', [PostController::class, "deleteMedia"])->middleware(['auth:sanctum']);
Route::post('post/store', [PostController::class, "store"])->middleware(['auth:sanctum']);
Route::post('post/update', [PostController::class, "update"])->middleware(['auth:sanctum']);
Route::post('post/delete', [PostController::class, "delete"])->middleware(['auth:sanctum']);
Route::post('post/favor-post', [PostController::class, "favorPost"])->middleware(['auth:sanctum']);
Route::post('post/fetch-favored-post', [PostController::class, "getFavoredPosts"])->middleware(['auth:sanctum']);
Route::post('post/special-post', [PostController::class, "specialPost"]);
Route::post('post/fetch-special-posts', [PostController::class, "getSpeacialPosts"]);
Route::post('post/fetch-recent-posts', [PostController::class, "getRecentPosts"]);
Route::post('post/block', [PostController::class, "blockPost"])->middleware(['auth:sanctum-admin']);

/*
|--------------------------------------------------------------------------
| Comments Routes
|--------------------------------------------------------------------------
*/
Route::post('comments/fetch', [CommentController::class, "fetchComments"]);
Route::post('comment/store', [CommentController::class, "store"])->middleware(['auth:sanctum']);

/*
|--------------------------------------------------------------------------
| Chats Routes
|--------------------------------------------------------------------------
*/
Route::post('chat/storeOrGet', [ChatController::class, "getOrCreateChat"])->middleware(['auth:sanctum']);
Route::post('chats/fetch', [ChatController::class, "getAllChatsForUser"])->middleware(['auth:sanctum']);
Route::post('chat/delete', [ChatController::class, "delete"])->middleware(['auth:sanctum']);
Route::post('message/store', [ChatMessageController::class, "sendMessage"])->middleware(['auth:sanctum']);
Route::post('message/delete', [ChatMessageController::class, "delete"])->middleware(['auth:sanctum']);

/*
|--------------------------------------------------------------------------
| Ratings Routes
|--------------------------------------------------------------------------
*/
Route::post('ratings/fetch', [RatingController::class, "index"]);
Route::post('rating/store', [RatingController::class, "store"])->middleware(['auth:sanctum']);

/*
|--------------------------------------------------------------------------
| Reports Routes
|--------------------------------------------------------------------------
*/
Route::post('reports/fetch', [ReportController::class, "index"]);
Route::post('report/store', [ReportController::class, "store"])->middleware(['auth:sanctum']);

/*
|--------------------------------------------------------------------------
| Notifications Routes
|--------------------------------------------------------------------------
*/
Route::post('notifications/fetch', [NotificationController::class, "index"])->middleware(['auth:sanctum']);
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
