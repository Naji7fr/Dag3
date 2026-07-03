<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BehandelingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KlantController;
use App\Http\Controllers\MedewerkerController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::middleware('eigenaar')->group(function (): void {
        Route::get('/klanten', [KlantController::class, 'index'])->name('klanten.index');
        Route::get('/klanten/{klant}', [KlantController::class, 'show'])->name('klanten.show');
        Route::get('/klanten/{klant}/wijzigen', [KlantController::class, 'edit'])->name('klanten.edit');
        Route::put('/klanten/{klant}', [KlantController::class, 'update'])->name('klanten.update');

        Route::get('/medewerkers', [MedewerkerController::class, 'index'])->name('medewerkers.index');
        Route::get('/medewerkers/{medewerker}', [MedewerkerController::class, 'show'])->name('medewerkers.show');
        Route::get('/medewerkers/{medewerker}/wijzigen', [MedewerkerController::class, 'edit'])->name('medewerkers.edit');
        Route::put('/medewerkers/{medewerker}', [MedewerkerController::class, 'update'])->name('medewerkers.update');

        Route::get('/behandelingen', [BehandelingController::class, 'index'])->name('behandelingen.index');
        Route::get('/behandelingen/{id}', [BehandelingController::class, 'show'])->name('behandelingen.show');
        Route::get('/behandelingen/product/{productId}', [BehandelingController::class, 'showProduct'])->name('behandelingen.product-detail');
        Route::get('/behandelingen/product/{productId}/wijzigen', [BehandelingController::class, 'editProduct'])->name('behandelingen.edit-product');
        Route::put('/behandelingen/product/{productId}', [BehandelingController::class, 'updateProduct'])->name('behandelingen.update-product');
    });
});
