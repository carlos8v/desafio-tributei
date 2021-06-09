<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NFesController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__.'/auth.php';

Route::get('/', fn() => view('auth.login'))->middleware(['guest']);

Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth'])
    ->name('dashboard');


// NFes

Route::get('/dashboard/nfes', [NFesController::class, 'index'])
    ->middleware(['auth'])
    ->name('nfes');

Route::get('/dashboard/nfes/new', [NFesController::class, 'new'])
    ->middleware(['auth'])
    ->name('nfes.new');

Route::post('/dashboard/nfes/new', [NFesController::class, 'store'])
    ->middleware(['auth'])
    ->name('nfes.store');

Route::get('/dashboard/nfes/{id}', [NFesController::class, 'show'])
    ->middleware(['auth'])
    ->name('nfes.show');


// Products

Route::get('/dashboard/products', [ProductController::class, 'index'])
    ->middleware(['auth'])
    ->name('products');

Route::put('/dashboard/products', [ProductController::class, 'update'])
    ->middleware(['auth'])
    ->name('products.update');
