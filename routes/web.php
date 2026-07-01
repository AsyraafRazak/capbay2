<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\AgentAuthController;

Route::get('/', function () {
    return redirect()->route('customer.register');
});

Route::get('/register', [CustomerController::class, 'create'])->name('customer.register');
Route::post('/register', [CustomerController::class, 'store'])->name('customer.store');

// Agent auth (guest-only)
Route::prefix('agent')->name('agent.')->group(function () {
    Route::get('/login', [AgentAuthController::class, 'showLogin'])->name('login')->middleware('guest');
    Route::post('/login', [AgentAuthController::class, 'login'])->name('login.submit')->middleware('guest');
    Route::post('/logout', [AgentAuthController::class, 'logout'])->name('logout');
});

// Agent dashboard (auth-protected)
Route::prefix('agent')->name('agent.')->middleware('auth')->group(function () {
    Route::get('/', [AgentController::class, 'index'])->name('index');
    Route::get('/{id}', [AgentController::class, 'show'])->name('show');
    Route::put('/{id}', [AgentController::class, 'update'])->name('update');
    Route::post('/{id}/transition', [AgentController::class, 'transition'])->name('transition');
});
