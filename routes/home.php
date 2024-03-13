<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\Admin\PpriceController;

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

Route::get('home', [HomeController::class, 'index']);
Route::get('carousel', [PostController::class, 'topCarousel']);

//Ads route
Route::get('ads', [HomeController::class, 'ads']);


//Payment route
Route::get('payment/success', [PaymentController::class, 'success'])->name('payment_success')->middleware('auth:sanctum');
Route::get('payment/cancel', [PaymentController::class, 'cancel'])->name('payment_cancel')->middleware('auth:sanctum');
//Route::('payment/pay', [PaymentController::class, 'payment']);


//post route
Route::get('posts', [PostController::class, 'index']);
Route::post('posts', [PostController::class, 'store'])->middleware('auth:sanctum');
Route::put('posts', [PostController::class, 'update'])->middleware('auth:sanctum');
Route::get('posts/{audition}', [PostController::class, 'show']);

//vote route
Route::get('votes', [VoteController::class, 'index'])->middleware('auth:sanctum');
Route::get('votes/{vote}', [VoteController::class, 'show'])->middleware('auth:sanctum');
Route::post('/votes/{vote}/voter', [VoteController::class,'voter'])->middleware('auth:sanctum');

//notice route
Route::get('notices', [NoticeController::class, 'index']);
Route::get('notices/{notice}', [NoticeController::class, 'show']);

//product route
Route::apiResource('products', ProductController::class);
//Route::apiResource('products', ProductController::class)->middleware('auth:sanctum')
//    ->except([
//        'products.init', // Exclude the 'index' route
//        'products.show',  // Exclude the 'show' route
//    ]);
Route::apiResource('products.pprices', PpriceController::class);
Route::get('product/init', [ProductController::class, 'init']);
Route::get('product/popular', [ProductController::class, 'popular']);
Route::post('products', [ProductController::class, 'store'])->middleware('auth:sanctum');
Route::put('products', [ProductController::class, 'update'])->middleware('auth:sanctum');


//membership
Route::apiResource('memberships', MembershipController::class);

