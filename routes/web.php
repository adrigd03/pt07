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
| Here is where you can registre web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('', function () {
    
    return view('home');
})->name('home');

Route::get('login-google', function () {
    return Socialite::driver('google')->redirect();
})->name('login-google');

Route::get('google-callback', function (Request $request) {

    try {
        $user = Socialite::driver('google')->user();
    } catch (\Exception $e) {
        return redirect()->back()->withErrors('Error al obtenir les dades del usuari.');
    }


    // //verificar si el usuario existe en la base de datos y si no existe crearlo y loguearlo
    $usuari = Usuari::find($user->email);

    if (!$usuari) {
        $usuari = new Usuari();
        $usuari->nom = $user->name;
        $usuari->cognoms = $user->user['family_name'];
        $usuari->email = $user->email;

        if($user->nickname != null){
            $user->username = Usuari::getAvailableNickname($user->nickname);
        }else{
           
            $usuari->username = strtolower(explode(' ', $user->name)[0]);
            
            $usuari->username = Usuari::getAvailableNickname($usuari->username);
        }
        
        $usuari->save();
    }

    Auth::login($usuari);

    $request->session()->put('avatarUrl', $user->getAvatar());


    // redireccionar al home
    return redirect()->route('home');
});

Route::get('login', function (Request $request) {
    return view('login');
})->middleware('guest')->name('login');

Route::get('registre', function () {
    return view('registre');
})->middleware('guest')->name('registre');




Route::post('logout', function () {
    Auth::logout();
    return redirect()->route('home');
})->name('logout');