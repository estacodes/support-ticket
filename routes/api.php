<?php

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

Route::post('login', [App\Http\Controllers\BasicAuthController::class, 'login'])->name('user.login');
Route::middleware('auth:api')->group(function() {
    Route::get('tickets/all',[App\Http\Controllers\TicketApiController::class, 'viewAllSupportTickets']);
    Route::get('ticket/{ticketId}',[App\Http\Controllers\TicketApiController::class, 'viewTicket']);
    Route::post('ticket/open',[App\Http\Controllers\TicketApiController::class, 'openSupportTicket']);
    Route::post('ticket/update',[App\Http\Controllers\TicketApiController::class, 'updateTicket']);
    Route::post('ticket/transfer',[App\Http\Controllers\TicketApiController::class, 'assignOrTransferTicket'])->middleware('can:can_assign_tickets');
    Route::post('ticket/assign',[App\Http\Controllers\TicketApiController::class, 'assignOrTransferTicket'])->middleware('can:can_assign_tickets');
    Route::get('ticket/close/{ticketId}',[App\Http\Controllers\TicketApiController::class, 'closeTicket'])->middleware('can:can_close_tickets');
    Route::get('logout',[App\Http\Controllers\BasicAuthController::class, 'logout']);
});
