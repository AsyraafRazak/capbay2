<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AgentController;

Route::get('/', function () {
    return redirect()->route('customer.register');
});

Route::get('/register', [CustomerController::class, 'create'])->name('customer.register');
Route::post('/register', [CustomerController::class, 'store'])->name('customer.store');

Route::prefix('agent')->name('agent.')->group(function () {
    Route::get('/', [AgentController::class, 'index'])->name('index');
    Route::get('/{id}', [AgentController::class, 'show'])->name('show');
    Route::put('/{id}', [AgentController::class, 'update'])->name('update');
    Route::post('/{id}/transition', [AgentController::class, 'transition'])->name('transition');
});
