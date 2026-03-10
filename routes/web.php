<?php

use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ClientFileController;
use App\Http\Controllers\FiscalObligationFileController;
use App\Http\Controllers\InvoiceFileController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/aviso-de-privacidad', function () {
    return view('legal.privacy');
})->name('legal.privacy');

Route::get('/terminos-y-condiciones', function () {
    return view('legal.terms');
})->name('legal.terms');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    // Recuperación de contraseña
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

// Rutas de suscripción — sin middleware 'subscribed' para no crear bucle
Route::middleware(['auth'])->prefix('subscription')->name('subscription.')->group(function () {
    Route::get('/', [SubscriptionController::class, 'index'])->name('index');
    Route::post('/checkout', [SubscriptionController::class, 'checkout'])->name('checkout');
    Route::get('/success', [SubscriptionController::class, 'success'])->name('success');
    Route::get('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
    Route::get('/portal', [SubscriptionController::class, 'portal'])->name('portal');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/invoices/{invoice}/download/xml', [InvoiceFileController::class, 'downloadXml'])
        ->name('invoices.download.xml');

    Route::get('/invoices/{invoice}/download/pdf', [InvoiceFileController::class, 'downloadPdf'])
        ->name('invoices.download.pdf');

    Route::get('/clients/{client}/download/cer', [ClientFileController::class, 'downloadCer'])
        ->name('clients.download.cer');

    Route::get('/clients/{client}/download/key', [ClientFileController::class, 'downloadKey'])
        ->name('clients.download.key');

    Route::get('/fiscal-obligations/{fiscalObligation}/download/acuse', [FiscalObligationFileController::class, 'downloadAcuse'])
        ->name('fiscal-obligations.download.acuse');
});
