<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FonnteWebhookController;

Route::post('/fonnte/webhook', [FonnteWebhookController::class, 'handle']);
Route::get('/get-operators/getall', [UserController::class, 'getOperatorsByCabang']);
