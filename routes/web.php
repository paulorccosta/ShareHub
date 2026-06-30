<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RateioController;
use App\Http\Controllers\SettlementController;
use App\Http\Controllers\SpaceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('spaces', SpaceController::class);

    Route::post('/spaces/{space}/members', [MemberController::class, 'store'])->name('spaces.members.store');
    Route::delete('/spaces/{space}/members/{member}', [MemberController::class, 'destroy'])->name('spaces.members.destroy');

    Route::resource('spaces.expenses', ExpenseController::class);

    Route::get('/spaces/{space}/rateio', [RateioController::class, 'show'])->name('rateio.show');

    Route::post('/spaces/{space}/settlements', [SettlementController::class, 'store'])->name('spaces.settlements.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/promote', [AdminController::class, 'promoteUser'])->name('users.promote');
    Route::post('/users/{user}/demote', [AdminController::class, 'demoteUser'])->name('users.demote');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

    Route::get('/spaces', [AdminController::class, 'spaces'])->name('spaces');
    Route::delete('/spaces/{space}', [AdminController::class, 'destroySpace'])->name('spaces.destroy');
});

require __DIR__.'/auth.php';
