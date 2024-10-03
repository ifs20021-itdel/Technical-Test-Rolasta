<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::apiResource('/posts', App\Http\Controllers\PostController::class);
Route::apiResource('/voucers', App\Http\Controllers\VoucerController::class);
Route::post('/posts/{id}/claim', [App\Http\Controllers\VoucerController::class, 'claim'])->middleware('auth:sanctum');


Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum');
