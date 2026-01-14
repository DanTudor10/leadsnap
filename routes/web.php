<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/admin'); 
    }

    return redirect('/admin/login'); 
});
