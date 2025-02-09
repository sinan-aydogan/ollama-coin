<?php

use App\Http\Controllers\CoinController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\WalletController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    /*Wallets*/
    Route::resource('wallets', WalletController::class);
    Route::get("/get-wallet-balance/{wallet}", [WalletController::class, 'getBalance'])->name('wallets.get-balance');

    /*Coins*/
    Route::resource('coins', CoinController::class);
    Route::get("/sync-coin-list", [CoinController::class, 'syncCoinList'])->name('coins.sync-coin-list');
    Route::get("/sync-coin-price/{symbol}", [CoinController::class, 'syncCoinPrice'])->name('coins.sync-coin-price');
    Route::get("/sync-coin-prices", [CoinController::class, 'syncCoinPrices'])->name('coins.sync-coin-prices');

    /*Settings*/
    Route::get('/system-settings', [SystemSettingController::class, 'index'])->name('system-settings.index');
    Route::put('/system-settings', [SystemSettingController::class, 'update'])->name('system-settings.update');
    Route::get('/get-ai-models', [SystemSettingController::class, 'getAiModels'])->name('system-settings.get-ai-models');
});
