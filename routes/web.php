<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ClientFileController;
use App\Http\Controllers\FiscalObligationFileController;
use App\Http\Controllers\InvoiceFileController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
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
