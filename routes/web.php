<?php

use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PaypalController;

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

Route::get('/', function () {
    return response()->json(['success' => false, 'message' => 'Failed', 'token' => null]);
});

Route::get('qrcode', function () {
    return QrCode::size(250)
        ->backgroundColor(255, 255, 204)
        ->generate('https://google.com');
});

Route::get('reset-password',[AuthController::class, 'resetPasswordLoad']);
Route::post('reset-password',[AuthController::class, 'resetPassword']);


