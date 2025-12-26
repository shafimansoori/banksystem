<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

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

// Bank System API Endpoints
Route::prefix('v1')->group(function () {
    // Public endpoint - Login to get token
    Route::post('/login', [ApiController::class, 'login']);
});

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Account endpoints
    Route::get('/accounts', [ApiController::class, 'getAccounts']);
    Route::get('/accounts/{id}', [ApiController::class, 'getAccount']);

    // Transaction endpoints
    Route::get('/transactions', [ApiController::class, 'getTransactions']);
    Route::get('/transactions/flagged', [ApiController::class, 'getFlaggedTransactions']);

    // User endpoints
    Route::get('/users', [ApiController::class, 'getUsers']);

    // Statistics endpoint
    Route::get('/statistics', [ApiController::class, 'getStatistics']);
});
