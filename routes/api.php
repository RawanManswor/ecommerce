<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\products\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\users\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);;
Route::post('/login', [AuthController::class, 'login'])->name('login');


Route::group([

    //'prefix' => 'products',
    //  'middleware' => ['auth:sanctum','throttle:60,1']
    'middleware' => ['auth:sanctum']
], function () {
    Route::match(['put', 'patch'], '/update-user/{id}', [UserController::class, 'updateRoles']);
    Route::post('/add-review', [ReviewController::class, 'store']);

    Route::group([
        'middleware' => 'isadmin'
    ], function () {
        Route::post('/add-product', [ProductController::class, 'store']);
        Route::match(['put', 'patch'], '/update-product/{id}', [ProductController::class, 'update']);
        Route::delete('/delete-product/{id}', [ProductController::class, 'destroy']);
        Route::get('/u', [UserController::class, 'getUsersByRole']);
    });

    Route::get('/all-products', [ProductController::class, 'index']);
    //There is something wrong right here....i wish you discover it
    Route::get('/product_by_category/{letter}', [ProductController::class, 'filterProductsByCategory']);
    Route::get('/product_by_id/{id}', [ProductController::class, 'show']);
    Route::get('/all-users', [UserController::class, 'index']);

    Route::get('/show_all_reviews', [ReviewController::class, 'index']);
    //إرجاع تقييم مستخدم معين على منتج معين
    Route::get('/users/{user_id}/reviewed/{product_id}', [ReviewController::class, 'getUserRatingForProduct']);
    //إرجاع كل المستخدمين الذين قيمو منتج معين
    Route::get('/get_all_users_reviewed_product/{product_id}', [ReviewController::class, 'getUsersByProduct']);
    //return all reviews specific product
    Route::get('/get_all_reviews_product/{product_id}', [ReviewController::class, 'getAllReviewsForProduct']);
});
