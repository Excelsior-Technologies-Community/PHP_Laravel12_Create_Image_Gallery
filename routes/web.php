<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;

Route::get('/gallery', [ImageController::class, 'index'])->name('gallery.index');
Route::get('/gallery/create', [ImageController::class, 'create'])->name('gallery.create');
Route::post('/gallery', [ImageController::class, 'store'])->name('gallery.store');

Route::delete('/gallery/bulk-destroy', [ImageController::class, 'bulkDestroy'])->name('gallery.bulkDestroy');
Route::delete('/gallery/{id}', [ImageController::class, 'destroy'])->name('gallery.destroy');

Route::get('/gallery/download/{id}', [ImageController::class, 'download'])->name('gallery.download');

Route::get('/gallery/trash', [ImageController::class, 'trash'])->name('gallery.trash');
Route::post('/gallery/restore/{id}', [ImageController::class, 'restore'])->name('gallery.restore');
Route::delete('/gallery/force-delete/{id}', [ImageController::class, 'forceDelete'])->name('gallery.forceDelete');

Route::post('/gallery/bulk-restore', [ImageController::class, 'bulkRestore'])->name('gallery.bulkRestore');
Route::delete('/gallery/bulk-force-delete', [ImageController::class, 'bulkForceDelete'])->name('gallery.bulkForceDelete');