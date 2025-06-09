<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('/users', [UserController::class, 'register']);
Route::post('/users/login', [UserController::class, 'login']);

Route::middleware(ApiAuthMiddleware::class)->group(function () {
    Route::get('/users/current', [UserController::class, 'get']);
    Route::patch('/users/current', [UserController::class, 'update']);

    Route::delete('/users/logout', [UserController::class, 'logout']);

    Route::post('/contacts', [ContactController::class, 'create']);  //create contact
    Route::get('/contacts', [ContactController::class, 'search']);  //search contacts (with or without filter like name, email, etc)

    // Route::get('/contacts/{id:[0-9]}', [ContactController::class, 'create']);  //get contact detail
    // or
    Route::get('/contacts/{id}', [ContactController::class, 'get'])->where('id', '[0-9]+');  //get contact detail
    Route::put('/contacts/{id}', [ContactController::class, 'update'])->where('id', '[0-9]+');  //update contact detail
    Route::delete('/contacts/{id}', [ContactController::class, 'delete'])->where('id', '[0-9]+');  //delete a contact

    Route::post('/contacts/{idContact}/addresses', [AddressController::class, 'create'])  //create an address for a contact
        ->where('idContact', '[0-9]+');
    Route::get('/contacts/{idContact}/addresses', [AddressController::class, 'list'])  //get addresses list from a contact
        ->where('idContact', '[0-9]+');

    Route::get('/contacts/{idContact}/addresses/{idAddress}', [AddressController::class, 'get'])  //get address detail form a contact
        ->where('idContact', '[0-9]+')
        ->where('idAddress', '[0-9]+');
    Route::put('/contacts/{idContact}/addresses/{idAddress}', [AddressController::class, 'update'])  //update address detail form a contact
        ->where('idContact', '[0-9]+')
        ->where('idAddress', '[0-9]+');
    Route::delete('/contacts/{idContact}/addresses/{idAddress}', [AddressController::class, 'delete'])  //update an address from a contact
        ->where('idContact', '[0-9]+')
        ->where('idAddress', '[0-9]+');
});
