<?php

use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["verification"]], function(){

    // Comments Routes
    Route::post('createComment', [CommentController::class, 'createComment']);
    Route::post('updateComment/{id}', [CommentController::class, 'updateComment']);
    Route::post('deleteComment/{id}', [CommentController::class, 'deleteComment']);
});
