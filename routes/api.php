<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')
    ->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('forgot-password', ForgotPasswordController::class);
        Route::post('password-reset', PasswordResetController::class);
        Route::post('logout', LogoutController::class)
            ->middleware('auth:sanctum');
    });


Route::get('tags', [TagController::class, 'index']);

Route::get('documents/token/{token}', [DocumentController::class, 'showByToken']);
Route::get('documents/download-by-token/{token}', [DocumentController::class, 'downloadByToken']);

Route::prefix('documents')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/', [DocumentController::class, 'index']);
        Route::get('/shared', [DocumentController::class, 'indexSharedWith']);
        Route::get('/created', [DocumentController::class, 'indexCreated']);
        Route::get('/{id}', [DocumentController::class, 'show']);
        Route::post('/', [DocumentController::class, 'store']);
        Route::post('/{id}', [DocumentController::class, 'update']);
        Route::delete('/{id}', [DocumentController::class, 'delete']);
        Route::post('/{id}/private-share', [AccessController::class, 'store']);
        Route::post('/{id}/link-share', [AccessController::class, 'linkStore']);
        Route::get('download/{id}', [DocumentController::class, 'download']);
    });


Route::delete('accesses/{id}', [AccessController::class, 'destroy'])
    ->middleware('auth:sanctum');
