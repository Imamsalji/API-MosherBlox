<?php

use App\Http\Controllers\Api\AccountSettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\Transaction\CartController;
use App\Http\Controllers\Api\Transaction\OrderController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\GameController as AdminGameController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\WhatsAppWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//User Route
Route::prefix('v1')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/account/avatar', [AccountSettingController::class, 'updateAvatar']);
        Route::get('/account', [AccountSettingController::class, 'profile']);
        Route::put('/account', [AccountSettingController::class, 'updateProfile']);
        Route::put('/account/password', [AccountSettingController::class, 'updatePassword']);
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'index']);
            Route::post('/add', [CartController::class, 'add']);
            Route::put('/{id}', [CartController::class, 'update']);
            Route::delete('/{id}', [CartController::class, 'remove']);
        });
        Route::post('/checkout', [OrderController::class, 'checkout']);

        Route::post('/orders/{id}/payment', [OrderController::class, 'uploadPayment']);
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::put('/orders/{id}/report', [OrderController::class, 'report']);

        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // PUBLIC
    Route::get('/games', [GameController::class, 'index']);
    Route::get('/getRoblox/{user}', [OrderController::class, 'getRoblox']);
    Route::get('/games/{slug}', [GameController::class, 'show']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
});

Route::post('/webhook/whatsapp', [WhatsAppWebhookController::class, 'receive']);

Route::prefix('v1/admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/account/avatar', [AccountSettingController::class, 'updateAvatar']);
    Route::get('/account', [AccountSettingController::class, 'profile']);
    Route::put('/account', [AccountSettingController::class, 'updateProfile']);
    Route::put('/account/password', [AccountSettingController::class, 'updatePassword']);
    Route::get('/orders', [AdminOrderController::class, 'index']);
    Route::get('/orders/{id}', [AdminOrderController::class, 'show']);
    Route::put('/orders/{id}/verify', [AdminOrderController::class, 'verify']);

    // GAME
    Route::get('/games', [AdminGameController::class, 'index']);
    Route::post('/games', [AdminGameController::class, 'store']);
    Route::get('/games/{id}', [AdminGameController::class, 'show']);
    Route::put('/games/{id}', [AdminGameController::class, 'update']);
    Route::delete('/games/{id}', [AdminGameController::class, 'destroy']);
    // PRODUCT
    Route::get('/products', [AdminProductController::class, 'index']);
    Route::post('/products', [AdminProductController::class, 'store']);
    Route::get('/products/{id}', [AdminProductController::class, 'show']);
    Route::put('/products/{id}', [AdminProductController::class, 'update']);
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
