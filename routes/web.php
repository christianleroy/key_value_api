<?php

use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});


Route::get('/', function () {
    return redirect('/api/documentation#/Key%20Values');
});

Route::get('/key-values', [\App\Http\Controllers\KeyValueWebController::class, 'index'])->name('key-values.index');