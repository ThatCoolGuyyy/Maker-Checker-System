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

// Route::post('/sendmail', [PendingRequestController::class, 'send_mail']);

Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('create/', [PendingRequestController::class, 'create']);
    Route::post('update/{id}', [PendingRequestController::class, 'update']);
    Route::delete('delete/{id}', [PendingRequestController::class, 'destroy']);
    Route::get('pendingrequests', [PendingRequestController::class, 'pending_requests']);
    Route::post('pendingrequests/{id}', [PendingRequestController::class, 'individual_pending_requests'])->middleware('approvalAccess');
    Route::post('approve/{id}', [PendingRequestController::class, 'approve_request'])->middleware('approvalAccess');
    Route::post('reject/{id}', [PendingRequestController::class, 'reject_request'])->middleware('approvalAccess');
});
    