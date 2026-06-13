<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\NewsController;
use App\Http\Controllers\Customer\CategoryController;
use App\Http\Controllers\Customer\TagController;


// صفحه اصلی سایت
Route::get('/', [HomeController::class, 'home'])->name('customer.home');

Route::prefix('news')->group(function () {

    // Route::get('/', [NewsController::class, 'index'])->name('news.index');

    Route::get('/archive', [NewsController::class, 'archive'])->name('news.archive');

    Route::get('/{news}', [NewsController::class, 'show'])->name('news.show');

    });
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');

Route::get('/tag/{slug}', [TagController::class, 'show'])->name('tag.show');
// Route::get('/', function () {
//     return view('welcome');
// });
