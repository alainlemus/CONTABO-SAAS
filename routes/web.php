<?php

use App\Http\Controllers\ClientFileController;
use App\Http\Controllers\FiscalObligationFileController;
use App\Http\Controllers\InvoiceFileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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
