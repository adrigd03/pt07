<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Usuari;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('login');
})->middleware('guest')->name('login');

Route::get('/login-google', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/google-callback', function (Request $request) {

    try {
        $user = Socialite::driver('google')->user();
    } catch (\Exception $e) {
        return redirect()->route('/')->withErrors('Error al obtenir les dades del usuari.');
    }


    // //verificar si el usuario existe en la base de datos y si no existe redirigir a la ruta "/" con un mensaje de error y si existe loguearlo
    // $userDB = Usuari::where('email', $user->email)->first();

    // if (!$userDB) {
    //     return redirect()->route('/')->withErrors('No tens permís per accedir a aquesta aplicació.');
    // }


    // Auth::login($userDB);

    // $request->session()->put('avatarUrl', $user->getAvatar());

    // redireccionar al home
    return redirect()->route('home');
});

Route::get('/home', function (Request $request) {
    return view('home');
})->middleware('auth')->name('home');