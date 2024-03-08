<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Usuari;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UsuariController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can registre web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('', function () {
    
    return view('home');
})->name('home');

Route::get('login-google', [UsuariController::class, 'loginGoogle'])->name('login-google');

Route::get('google-callback', [UsuariController::class, 'googleCallback'])->name('google-callback');

Route::get('login', function (Request $request) {
    return view('login');
})->middleware('guest')->name('login');

Route::get('registre', function () {
    return view('registre');
})->middleware('guest')->name('registre');

Route::post('registre', [UsuariController::class, 'registre'])->name('registre');

Route::post('login', [UsuariController::class, 'login'])->name('login');

Route::post('logout', [UsuariController::class, 'logout'])->name('logout');

