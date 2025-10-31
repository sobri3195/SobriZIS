<?php

use App\Http\Controllers\DonationController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PayoutController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
Route::get('/programs/{program:slug}', [ProgramController::class, 'show'])->name('programs.show');
Route::get('/transparency', [ProgramController::class, 'transparency'])->name('transparency');

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return Inertia::render('Auth/Login');
    })->name('login');
    
    Route::get('/register', function () {
        return Inertia::render('Auth/Register');
    })->name('register');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    
    Route::get('/profile', function () {
        return Inertia::render('Profile/Edit');
    })->name('profile.edit');
    
    Route::get('/donations/my', [DonationController::class, 'myDonations'])->name('donations.my');
    Route::get('/donations/{donation}', [DonationController::class, 'show'])->name('donations.show');
    Route::get('/donations/{donation}/receipt', [DonationController::class, 'downloadReceipt'])->name('donations.receipt');
});

Route::prefix('donate')->name('donate.')->group(function () {
    Route::get('/{program:slug}', [DonationController::class, 'create'])->name('create');
    Route::post('/{program}', [DonationController::class, 'store'])->name('store');
});

Route::middleware(['auth', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('programs', \App\Http\Controllers\Admin\ProgramController::class);
    Route::resource('donations', \App\Http\Controllers\Admin\DonationController::class)->only(['index', 'show']);
    Route::resource('mustahik', \App\Http\Controllers\Admin\MustahikController::class);
    Route::resource('payouts', PayoutController::class);
    
    Route::post('/payouts/{payout}/approve', [PayoutController::class, 'approve'])->name('payouts.approve');
    Route::post('/payouts/{payout}/disburse', [PayoutController::class, 'disburse'])->name('payouts.disburse');
    
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/download', [\App\Http\Controllers\Admin\ReportController::class, 'download'])->name('reports.download');
    
    Route::get('/audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
});
