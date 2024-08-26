<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BarrierController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [BarrierController::class, 'index']);
Route::get('/get-logs', [BarrierController::class, 'getLogs']);
Route::get('/print-ticket/{id}/{timestamp}', [BarrierController::class, 'printTicket'])->name('print.ticket');
