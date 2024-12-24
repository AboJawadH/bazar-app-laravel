<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdvertismentController;
use App\Http\Controllers\AppSettingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SectionController;
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
| App Settings Routes
|--------------------------------------------------------------------------
*/
Route::post('maintenance-mode/update', [AppSettingController::class, "updateAppSetting"]);

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::post('users/fetch', [AuthController::class, "index"]);
Route::post('user/store', [AuthController::class, "signUpUser"])->middleware(['maintenance.check']);
Route::post('user/local-update', [AuthController::class, "updateUserLocal"])->middleware(['auth:sanctum']);
Route::post('user/update', [AuthController::class, "updateProfileInfo"])->middleware(['auth:sanctum']);
Route::post('user/info', [AuthController::class, "getUser"]);
Route::post('user/fetch', [AuthController::class, "getOneUser"]);
Route::post('user/delete', [AuthController::class, "deleteUser"])->middleware(['auth:sanctum-admin']);
Route::post('user/login', [AuthController::class, "loginUser"])->middleware(['maintenance.check']);
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
| Home Page Routes
|--------------------------------------------------------------------------
*/
Route::post('sections/active-fetch', [HomePageController::class, "getActiveSections"])->middleware(['maintenance.check']);
Route::post('ads-posts/fetch', [HomePageController::class, "getAdsAndPosts"])->middleware(['maintenance.check']);


/*
|--------------------------------------------------------------------------
| Advertisment Routes
|--------------------------------------------------------------------------
*/

Route::post('advertisments/fetch', [AdvertismentController::class, "index"])->middleware(['maintenance.check']);
Route::post('advertisments/homepage', [AdvertismentController::class, "getAdsForHomePage"])->middleware(['maintenance.check']);
Route::post('advertisments/fetch-by-location', [AdvertismentController::class, "getAdvertisementsByLocation"]);
Route::post('advertisment/store', [AdvertismentController::class, "store"])->middleware(['auth:sanctum-admin']);
Route::post('advertisment/update', [AdvertismentController::class, "update"])->middleware(['auth:sanctum-admin']);
Route::post('advertisment/delete', [AdvertismentController::class, "delete"])->middleware(['auth:sanctum-admin']);


/*
|--------------------------------------------------------------------------
| region Routes
|--------------------------------------------------------------------------
*/
Route::post('regions/fetch-all', [RegionController::class, "getAllRegions"]);
Route::post('regions/fetch', [RegionController::class, "getMainRegions"]);
Route::post('regions/for-parent-region', [RegionController::class, "getRegionsForParentRegion"]);
Route::post('region/store', [RegionController::class, "store"]);
Route::post('region/update', [RegionController::class, "update"]);


/*
|--------------------------------------------------------------------------
| sections routes
|--------------------------------------------------------------------------
*/
Route::post('region/store', [RegionController::class, "store"]);
Route::post('region/update', [RegionController::class, "update"]);
Route::post('sections/fetch-all', [SectionController::class, "getAllSections"])->middleware(['maintenance.check']);
Route::post('main-sections/fetch', [SectionController::class, "getMainSections"]);
Route::post('section/store', [SectionController::class, "store"]);
Route::post('section/update', [SectionController::class, "update"]);

/*
|--------------------------------------------------------------------------
| Posts Routes
|--------------------------------------------------------------------------
*/

Route::post('post/fetch', [PostController::class, "index"])->middleware(['maintenance.check']);
Route::post('posts/filter', [PostController::class, "filterPosts"]);
Route::post('post/fetch-posts-for-user', [PostController::class, "getPostsForUser"])
    ->middleware(['user-or-admin', 'maintenance.check']);
// Route::post('post/fetch-posts-for-user', [PostController::class, "getPostsForUser"])->middleware(['user-or-admin']);
Route::post('post/fetch-all-posts', [PostController::class, "getAllPosts"])->middleware(['maintenance.check']);
Route::post('post/fetch-similar-posts', [PostController::class, "getSimilarPosts"])->middleware(['maintenance.check']);
Route::post('post/fetch-one-post', [PostController::class, "getOnePost"])->middleware(['maintenance.check']);
Route::post('post/store-new-medias', [PostController::class, "storeMedias"])->middleware(['auth:sanctum','maintenance.check']);
Route::post('post/delete-one-media', [PostController::class, "deleteMedia"])->middleware(['auth:sanctum','maintenance.check']);
Route::post('post/store', [PostController::class, "store"])->middleware(['auth:sanctum','maintenance.check']);
Route::post('post/update', [PostController::class, "update"])->middleware(['auth:sanctum','maintenance.check']);
Route::post('post/delete', [PostController::class, "delete"])->middleware(['auth:sanctum','maintenance.check']);
Route::post('post/favor-post', [PostController::class, "favorPost"])->middleware(['auth:sanctum','maintenance.check']);
Route::post('post/fetch-favored-post', [PostController::class, "getFavoredPosts"])->middleware(['auth:sanctum','maintenance.check']);
Route::post('post/special-post', [PostController::class, "specialPost"]);
Route::post('post/fetch-special-posts', [PostController::class, "getSpeacialPosts"]);
Route::post('post/fetch-recent-posts', [PostController::class, "getRecentPosts"]);
Route::post('post/block', [PostController::class, "blockPost"])->middleware(['auth:sanctum-admin']);

/*
|--------------------------------------------------------------------------
| Comments Routes
|--------------------------------------------------------------------------
*/
Route::post('comments/fetch', [CommentController::class, "fetchComments"])->middleware(['maintenance.check']);
Route::post('comment/store', [CommentController::class, "store"])->middleware(['auth:sanctum','maintenance.check']);

/*
|--------------------------------------------------------------------------
| Chats Routes
|--------------------------------------------------------------------------
*/
Route::post('chat/storeOrGet', [ChatController::class, "getOrCreateChat"])->middleware(['auth:sanctum','maintenance.check']);
Route::post('chats/fetch', [ChatController::class, "getAllChatsForUser"])->middleware(['maintenance.check','auth:sanctum']);
Route::post('chat/delete', [ChatController::class, "delete"])->middleware(['auth:sanctum','maintenance.check']);
Route::post('message/store', [ChatMessageController::class, "sendMessage"])->middleware(['auth:sanctum','maintenance.check']);
Route::post('message/delete', [ChatMessageController::class, "delete"])->middleware(['auth:sanctum','maintenance.check']);

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
Route::post('report/store', [ReportController::class, "store"])->middleware(['auth:sanctum','maintenance.check']);

/*
|--------------------------------------------------------------------------
| Notifications Routes
|--------------------------------------------------------------------------
*/
Route::post('notifications/fetch', [NotificationController::class, "index"])->middleware(['auth:sanctum','maintenance.check']);
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
