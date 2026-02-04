<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FonnteWebhookController;

Route::post('/fonnte/webhook', [FonnteWebhookController::class, 'handle']);
