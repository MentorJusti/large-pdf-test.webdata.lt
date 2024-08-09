<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/time', [App\Http\Controllers\TimeController::class, 'getTime']);

Route::post("/register", [App\Http\Controllers\AuthController::class, "register"])->name('register');
Route::post("/login", [App\Http\Controllers\AuthController::class, "login"])->name('login');
Route::get("/logout", [App\Http\Controllers\AuthController::class, "logout"])->name('logout');
Route::get('/confirm', [App\Http\Controllers\AuthController::class, 'confirm'])->name('confirm');
Route::post('/refresh', [App\Http\Controllers\AuthController::class, 'refresh'])->name('refresh');