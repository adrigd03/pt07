<?php

namespace App\Http\Controllers;

use App\Models\Usuari;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuariController extends Controller
{

    //  Redirigeix l'usuari a la pàgina de login de Google
    public function loginGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Funcio per a la autenticació amb Google i el registre de l'usuari si no existeix
    public function googleCallback(Request $request)
    {

        try {
            $user = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error al obtenir les dades del usuari.');
        }


        // //verificar si el usuario existe en la base de datos y si no existe crearlo y loguearlo
        $usuari = Usuari::find($user->email);

        if (!$usuari) {
            $usuari = new Usuari();
            $usuari->email = $user->email;
            $usuari->avatar = $user->avatar;

            if ($user->nickname != null) {
                $user->username = Usuari::getAvailableNickname($user->nickname);
            } else {

                $usuari->username = strtolower(explode(' ', $user->name)[0]);

                $usuari->username = Usuari::getAvailableNickname($usuari->username);
            }

            $usuari->save();
        }

        Auth::login($usuari);

        return redirect()->route('home');
    }

    // Funcio per tancar la sessió de l'usuari
    public function logout()
    {
        Auth::logout();
        return redirect()->route('home');
    }


    // Mostra la pàgina de perfil de l'usuari actual
    public function perfil()
    {
        return view('perfil');
    }

    // Funcio per actualitzar el perfil de l'usuari actual
    public function actualitzarProfile(Request $request)
    {
        $usuari = Usuari::find(Auth::user()->email);


        $usuari->nom = $request->nom;
        $usuari->cognoms = $request->cognoms;
        $usuari->username = $request->username;

        $usuari->save();

        return redirect()->route('perfil');
    }

    // Funcio per actualitzar la contrasenya de l'usuari actual
    public function actualitzarContrasenya(Request $request)
    {
        $usuari = Usuari::find(Auth::user()->email);


        $usuari->contrasenya = bcrypt($request->contrasenya);

        $usuari->save();

        return redirect()->route('perfil');
    }
    // Funcio per esborrar el compte de l'usuari actual
    public function esborrarCompte()
    {
        $usuari = Usuari::find(Auth::user()->email);


        Auth::logout();


        $usuari->delete();

        return redirect()->route('home');
    }

    // Funcio per registrar un usuari
    public function registre(Request $request)
    {
        try {
            // Validar les dades del formulari
            $request->validate(
                [
                    'email' => 'required|email|unique:usuaris',
                    'password' => 'required|min:6|max:25|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/|confirmed',
                    'username' => 'required|unique:usuaris'
                ],
                [
                    'email.required' => 'El email és obligatori',
                    'email.email' => 'El email no és vàlid',
                    'email.unique' => 'Aquest email ja està registrat',
                    'password.required' => 'La contrasenya és obligatòria',
                    'password.min' => 'La contrasenya ha de tenir com a mínim 6 caràcters',
                    'password.max' => 'La contrasenya ha de tenir com a màxim 25 caràcters',
                    'password.confirmed' => 'Les contrasenyes no coincideixen',
                    'password.regex' => 'La contrasenya ha de contenir com a mínim una lletra majúscula, una minúscula i un número',
                    'username.required' => 'El nom de usuari és obligatori',
                    'username.unique' => 'Aquest nom de usuari ja està registrat'


                ]
            );


            // Crear un nou usuari
            Usuari::create([
                'email' => $request->email,
                'password' => $request->password,
                'username' => $request->username
            ]);



            // Redirigir l'usuari a la pàgina de login
            return redirect()->route('login')->with('success', "Usuari registrat correctament. Inicia sessió per continuar.");
        } catch (ValidationException $e) {
            // Retornem la resposta error si hi ha hagut algun error de validació
            return redirect()->back()->withErrors($e->validator->getMessageBag(), 'registre')->withInput();
        } catch (\Exception $e) {
            //Retornem la resposta error si ha ocorregut algun error
            return redirect()->back()->withErrors(['error' => 'Error al registrar l\'usuari'], 'registre')->withInput();
        }
    }

    // Funcio per autenticar un usuari
    public function login(Request $request)
    {
        try {
            // Validar les dades del formulari
            $request->validate(
                [
                    'emailUsername' => 'required',
                    'password' => 'required'
                ],
                [
                    'emailUsername.required' => 'El email o el nom de usuari és obligatori',
                    'password.required' => 'La contrasenya és obligatòria'
                ]
            );

            // Comprovem si la contrasenya és correcta utilitzant el hash check
            $usuari = Usuari::where('email', $request->emailUsername)->orWhere('username', $request->emailUsername)->first();

            if (!$usuari || Hash::check($request->password, $usuari->password) == false) {
                return redirect()->back()->withErrors(['error' => 'Les dades no són correctes'], 'login')->withInput();
            }

            // Autenticar l'usuari
            Auth::login($usuari);

            // Redirigir l'usuari a la pàgina home
            return redirect()->route('home');


        } catch (ValidationException $e) {
            // Retornem la resposta error si hi ha hagut algun error de validació
            return redirect()->back()->withErrors($e->validator->getMessageBag(), 'login')->withInput();
        } catch (\Exception $e) {
            //Retornem la resposta error si ha ocorregut algun error
            return redirect()->back()->withErrors(['error' => 'Error al iniciar sessió'], 'login')->withInput();
        }
    }
}