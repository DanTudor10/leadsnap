<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Pages\Auth\InvitationRegister;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/admin'); 
    }

    return redirect('/admin/login'); 
});

// Route::get(
//     '/admin/auth/invitation/{token}',
//     InvitationRegister::class
// )->name('filament.admin.auth.invitation-register');