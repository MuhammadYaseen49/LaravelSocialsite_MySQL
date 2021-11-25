<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["verification"]], function(){
   
    // POST Routes
    Route::post("createPost", [PostController::class, "createPost"]);
    Route::get("listPost", [PostController::class, "listPost"]);
    Route::get("myPost", [PostController::class, "myPost"]);
    Route::put("updatePost/{id}", [PostController::class, "updatePost"]);
    Route::delete("deletePost/{id}", [PostController::class, "deletePost"]);

});
