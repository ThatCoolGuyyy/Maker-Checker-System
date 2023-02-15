<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\PendingRequestController;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
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

Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::delete('/logout', [AuthController::class, 'logout']);
    Route::post('create/', [PendingRequestController::class, 'create']);
    Route::post('update/{id}', [PendingRequestController::class, 'update']);
    Route::delete('delete/{id}', [PendingRequestController::class, 'destroy']);
    Route::get('pendingRequests', [PendingRequestController::class, 'pending_requests']);
    Route::get('approve/{id}', [PendingRequestController::class, 'approve_request']);
    Route::get('reject/{id}', [PendingRequestController::class, 'reject_request']);
});
    