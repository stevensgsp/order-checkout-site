<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::prefix('/orders')->group(function () {
    Route::get('/show-store', [OrderController::class, 'showStore'])->name('orders.show-store');
    Route::get('/checkout/{productId}', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/process', [OrderController::class, 'process'])->name('orders.process');
    Route::get('/', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/reprocess/{orderId}', [OrderController::class, 'reprocess'])->name('orders.reprocess');
    Route::get('/{orderId}', [OrderController::class, 'show'])->name('orders.show');
});
