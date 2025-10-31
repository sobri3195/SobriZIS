<?php

use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\WidgetController;
use App\Http\Controllers\Api\ZakatCalculatorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::get('/programs', [ProgramController::class, 'index']);
    Route::get('/programs/{program:slug}', [ProgramController::class, 'show']);
    Route::get('/programs/{program}/progress', [ProgramController::class, 'progress']);
    
    Route::post('/donations', [DonationController::class, 'store']);
    Route::get('/donations/{donation}', [DonationController::class, 'show']);
    Route::get('/donations/{donation}/status', [DonationController::class, 'status']);
    
    Route::post('/zakat-calculator/calculate', [ZakatCalculatorController::class, 'calculate']);
    
    Route::get('/widget/counter', [WidgetController::class, 'counter']);
    Route::get('/widget/program/{program:slug}', [WidgetController::class, 'program']);
    Route::get('/widget/donors', [WidgetController::class, 'recentDonors']);
});

Route::post('/webhooks/midtrans', [WebhookController::class, 'midtrans'])->name('webhooks.midtrans');
Route::post('/webhooks/xendit', [WebhookController::class, 'xendit'])->name('webhooks.xendit');
Route::post('/webhooks/whatsapp', [WebhookController::class, 'whatsapp'])->name('webhooks.whatsapp');
Route::get('/webhooks/whatsapp', [WebhookController::class, 'verifyWhatsapp'])->name('webhooks.whatsapp.verify');
