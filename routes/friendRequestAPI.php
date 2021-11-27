<?php

use App\Http\Controllers\FriendsController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["verification"]], function(){

    // Friend Request Routes
    Route::post('sendRequest/{id}', [FriendsController::class, 'sendRequest']);
    Route::post('myRequests', [FriendsController::class, 'myRequests']);
    Route::post('acceptRequest/{id}', [FriendsController::class, 'acceptRequest']);

});
