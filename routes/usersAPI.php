<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::post("register", [MainController::class, "register"]);
Route::get('emailVerification/{token}/{email}',[MainController::class,'emailVerification']);
Route::post("login", [MainController::class, "login"]);

Route::group(["middleware" => ["verification"]], function(){

    // Route::get("profile", [StudentController::class, "profile"]);
    Route::post("logout", [MainController::class, "logout"]);
    Route::post("seeProfile", [MainController::class, "seeProfile"]);

 
});
