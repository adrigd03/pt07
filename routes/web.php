<?php

use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Usuari;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UsuariController;

use function PHPSTORM_META\map;

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

// En la rurta home passar tots els articles a la vista home
Route::get('/', [ArticleController::class, 'home'])->name('home')->middleware('RedireccionarPaginaInvalida');


Route::get('login-google', [UsuariController::class, 'loginGoogle'])->name('login-google');

Route::get('google-callback', [UsuariController::class, 'googleCallback'])->name('google-callback');

Route::get('login', function (Request $request) {
    return view('login');
})->middleware('guest')->name('login');

Route::get('registre', function () {
    return view('registre');
})->middleware('guest')->name('registre');

Route::post('registre', [UsuariController::class, 'registre'])->name('registre')->middleware('guest');

Route::post('login', [UsuariController::class, 'login'])->name('login')->middleware('guest');

Route::post('logout', [UsuariController::class, 'logout'])->name('logout')->middleware('auth');

Route::post('articles',[ArticleController::class, 'create'])->name('articles.create')->middleware('auth');

Route::put('articles/{article}', [ArticleController::class, 'editar'])->name('articles.editar')->middleware('auth');

Route::delete('articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy')->middleware('auth');

Route::get('articles-propis', [ArticleController::class, 'articlesPropis'])->name('articles-propis')->middleware('auth');


