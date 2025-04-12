<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\QRCodesController;

Route::get('/admin/qrcodes', [QRCodesController::class, 'index'])->name('admin.qrcodes.index');