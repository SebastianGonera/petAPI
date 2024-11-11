<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetController;

Route::group(['prefix'=>'pet'], function(){
    Route::get('/findByStatus',[PetController::class, 'showByStatus']);
    Route::get('/{id}',[PetController::class, 'show']);
    Route::post('/',[PetController::class, 'store']);
    Route::post('/{id}',[PetController::class, 'updatePetInStore']);
    Route::post('/{id}/uploadImage',[PetController::class, 'uploadImage']);
    Route::put('/{id}',[PetController::class, 'update']);
    Route::delete('/{id}',[PetController::class, 'destroy']);
});
