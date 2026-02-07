<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\FunController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/pending-users', [AdminController::class, 'pendingUsers']);
});

Route::post('register' , [UserController::class , 'register']);
Route::post('/login' ,[UserController::class , 'login']);
Route::middleware('auth:sanctum')->post('/logout', [UserController::class, 'logout']);

Route::get('/properties' , [PropertyController::class , 'allData']);
Route::get('/cities' , [PropertyController::class , 'cities']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/Addproperties', [PropertyController::class, 'store']);
    Route::get('/my-properties', [PropertyController::class, 'myProperties']);
    Route::put('/update/{id}' , [PropertyController::class , 'update']);
    Route::delete('/delete/{id}' , [PropertyController::class , 'removeProperty']);
});


Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('admin')->group(function () {
        // User management
        Route::get('/pending-users', [AdminController::class, 'pendingUsers']);
        Route::post('/approve-user/{id}', [AdminController::class, 'aproveUser']);
        Route::post('/reject-user/{id}', [AdminController::class, 'rejectUser']);
        Route::get('/all-users', [AdminController::class, 'allUsers']);

        //// Optional: Add more admin routes here
        //Route::get('/dashboard', [AdminController::class, 'dashboard']);
        //Route::get('/stats', [AdminController::class, 'getStats']);
        //
        //// Property management (admin can manage all properties)
        //Route::get('/all-properties', [PropertyController::class, 'adminAllProperties']);
        //Route::delete('/properties/{id}', [PropertyController::class, 'adminRemoveProperty']);
    });
});
