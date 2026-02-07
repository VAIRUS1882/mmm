<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FunController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\UserController;
use App\Models\Reservations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Routing\Router;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register' , [UserController::class , 'register']);
Route::post('/login' ,[UserController::class , 'login']);
Route::middleware('auth:sanctum')->post('/logout', [UserController::class, 'logout']);

Route::get('/properties' , [PropertyController::class , 'allData']);
Route::get('/cities' , [PropertyController::class , 'cities']);
Route::get('/governorateData' , [PropertyController::class , 'governorateData']);


Route::post('/completeReservation/{id}' , [ReservationsController::class , 'completeReservation']);

Route::middleware('auth:sanctum')->group(function () {


    Route::post('/Addproperties', [PropertyController::class, 'store']);
    Route::get('/my-properties', [PropertyController::class, 'myProperties']);
    Route::put('/update/{id}' , [PropertyController::class , 'update']);
    Route::delete('/delete/{id}' , [PropertyController::class , 'removeProperty']);
    Route::post('/reservation-confirme/{id}' , [ReservationsController::class , 'ownerConfirmeReservation']);

    Route::get('/reservation-getPending' , [ReservationsController::class , 'getpendingReservation']);


    Route::post('/reservation-property/{id}' , [ReservationsController::class , 'store']);

    Route::post('/reservation-update/{id}' , [ReservationsController::class , 'updateReservation']);
    Route::post('/reservation-Tenantcancle/{id}' , [ReservationsController::class , 'tenantCancelReservation']);
    Route::get('/reservation-confirme' , [ReservationsController::class , 'getConfiremReservationTenant']);
    Route::get('/reservation-getAllReservation' , [ReservationsController::class , 'getAllTentReservation']);

    Route::post('/rating/{id}' , [RatingController::class , 'store']);
    Route::post('/rating-update/{id}' , [RatingController::class , 'update']);
    Route::delete('/rating-delete/{id}' , [RatingController::class , 'destroy']);

    Route::get('/rating-getUserRatings' , [RatingController::class , 'getUserRatings']);

    Route::post('/favorite/{id}' , [FavoriteController::class , 'addToFavorite']);
    Route::delete('/favorite-delete/{id}' , [FavoriteController::class , 'removeFromFav']);
    Route::get('/favorite-getallFav' , [FavoriteController::class , 'getAllFav']);

});

Route::get('/reservation-booked/{id}' , [ReservationsController::class , 'getBookedDates']);
Route::get('/rating-getPropertyRating/{id}', [RatingController::class , 'getPropertyRatings']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('admin')->group(function () {

        Route::get('/pending-users', [AdminController::class, 'pendingUsers']);
        Route::post('/approve-user/{id}', [AdminController::class, 'aproveUser']);
        Route::post('/reject-user/{id}', [AdminController::class, 'rejectUser']);
        Route::get('/all-users', [AdminController::class, 'allUsers']);


    });
});
