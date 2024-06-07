<?php

use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\UsuariController;
use App\Http\Controllers\GaleriaController;


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
})->name('login');

Route::get('registre', function () {
    return view('registre');
})->name('registre');

Route::post('registre', [UsuariController::class, 'registre'])->name('registre');

Route::post('login', [UsuariController::class, 'login'])->name('login');

Route::post('logout', [UsuariController::class, 'logout'])->name('logout')->middleware('auth');

Route::post('articles',[ArticleController::class, 'create'])->name('articles.create')->middleware('auth');

Route::put('articles/{article}', [ArticleController::class, 'editar'])->name('articles.editar')->middleware('auth');

Route::delete('articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy')->middleware('auth');

Route::get('articles-propis', [ArticleController::class, 'articlesPropis'])->name('articles-propis')->middleware('auth', 'RedireccionarPaginaInvalida');

Route::post('recuperar', [UsuariController::class, 'recuperar'])->name('recuperar');

Route::get('restaurarContrasenya/{token}', [UsuariController::class, 'restaurarForm'])->name('restaurarContrasenya');

Route::post('restaurarContrasenya', [UsuariController::class, 'restaurarContrasenya'])->name('restaurarContrasenya.post');

Route::get('configuracio', [UsuariController::class, 'configuracio'])->name('configuracio')->middleware('auth');

Route::put('configuracio/username', [UsuariController::class, 'updateUsername'])->name('configuracio.username')->middleware('auth');

Route::put('configuracio/password', [UsuariController::class, 'updatePassword'])->name('configuracio.password')->middleware('auth');

Route::put('configuracio/avatar', [UsuariController::class, 'updateAvatar'])->name('configuracio.avatar')->middleware('auth');

Route::delete('configuracio', [UsuariController::class, 'esborrarCompte'])->name('configuracio.delete')->middleware('auth');

Route::get('galeria', [GaleriaController::class, 'galeria'])->name('galeria')->middleware('auth');

Route::post('galeria', [GaleriaController::class, 'crear'])->name('galeria.crear')->middleware('auth');

Route::delete('galeria/{imatge}', [GaleriaController::class, 'destroy'])->name('galeria.destroy')->middleware('auth');

Route::put('galeria/{imatge}', [GaleriaController::class, 'editar'])->name('galeria.editar')->middleware('auth');

Route::post('changeUser', [UsuariController::class, 'changeUser'])->name('changeUser')->middleware('auth');

