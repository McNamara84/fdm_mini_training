<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\QRCodesController;
use App\Http\Controllers\Admin\ScanController;

Route::get('/admin/qrcodes', [QRCodesController::class, 'index'])->name('admin.qrcodes.index');
Route::prefix('admin')->group(function () {
    Route::get('scan', [ScanController::class, 'index'])->name('admin.scan.index');
    Route::post('scan', [ScanController::class, 'store'])->name('admin.scan.store');
});