<?php

use Illuminate\Support\Facades\Route;
use MartinBean\Laravel\Mux\Http\Controllers\WebhookController;

// Mux Webhook
Route::post('/mux/webhook', WebhookController::class)->name('mux.webhook');
