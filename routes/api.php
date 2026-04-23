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
use App\Http\Controllers\Api\Article\ArticleController;
use App\Http\Controllers\Api\Article\CategoryController;
use App\Http\Controllers\Api\Article\CommentController;
use App\Http\Controllers\Api\Article\TagController;
use App\Http\Controllers\Api\Notification\EmailController;
use App\Http\Controllers\Api\Notification\WhatsAppWebhookController as WhatsAppWebhookController;

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

    //Notification
    Route::post('/webhook/whatsapp', [WhatsAppWebhookController::class, 'receive']);
    Route::post('/email/send', [EmailController::class, 'index']);

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

        //Article
        Route::post('articles', [ArticleController::class, 'store'])->name('articles.store');
        Route::match(['put', 'patch'], 'articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
        Route::delete('articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');

        /*
        | Public — Articles
        */
        Route::get('articles',       [ArticleController::class, 'index'])->name('articles.index');
        Route::get('articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');

        /*
        | Public — Categories & Tags (read-only)
        */
        Route::get('categories',          [CategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
        Route::get('tags',                [TagController::class, 'index'])->name('tags.index');

        /*
        | Public — Comments (read)
        */
        Route::get('articles/{slug}/comments', [CommentController::class, 'index'])->name('comments.index');


        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // PUBLIC
    Route::get('/games', [GameController::class, 'index']);
    Route::get('/getRoblox/{user}', [OrderController::class, 'getRoblox']);
    Route::get('/games/{slug}', [GameController::class, 'show']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    //Article
    Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');

});


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

    // Articles CRUD
    Route::get('articles',                    [ArticleController::class, 'adminIndex'])->name('articles.admin.index');
    Route::post('articles',                         [ArticleController::class, 'store'])->name('articles.store');
    Route::match(['put', 'patch'], 'articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('articles/{article}',             [ArticleController::class, 'destroy'])->name('articles.destroy');

    // Categories CRUD
    Route::post('categories',             [CategoryController::class, 'store'])->name('categories.store');
    Route::match(['put', 'patch'], 'categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Tags CRUD
    Route::post('tags',                   [TagController::class, 'store'])->name('tags.store');
    Route::match(['put', 'patch'], 'tags/{tag}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('tags/{tag}',           [TagController::class, 'destroy'])->name('tags.destroy');

    // Comments — post & delete
    Route::post('articles/{slug}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('comments/{comment}',     [CommentController::class, 'destroy'])->name('comments.destroy');

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
