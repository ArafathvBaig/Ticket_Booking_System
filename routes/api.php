<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\OrderController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => 'api'], function () {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    Route::post('sendVerificationMail', [UserController::class, 'sendVerificationMail']);
    Route::post('verifyUser', [UserController::class, 'verifyUser']);

    Route::post('createTicket', [TicketController::class, 'createTicket']);
    Route::get('displayTicketById', [TicketController::class, 'displayTicketById']);
    Route::get('displayAllTickets', [TicketController::class, 'displayAllTickets']);
    Route::post('updateTicketById', [TicketController::class, 'updateTicketById']);
    Route::post('deleteTicketById', [TicketController::class, 'deleteTicketById']);

    Route::post('addOrder', [OrderController::class, 'addOrder']);
    Route::get('displayOrders', [OrderController::class, 'displayOrders']);
    Route::post('updateOrderById', [OrderController::class, 'updateOrderById']);
    Route::post('deleteOrderById', [OrderController::class, 'deleteOrderById']);
});

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::get('getUser', [UserController::class, 'get_user']);
});
