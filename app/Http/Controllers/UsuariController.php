<?php

namespace App\Http\Controllers;

use App\Models\Usuari;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use SendsPasswordResetEmails;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\ArticleController;

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
        session()->flush();
        return redirect()->route('home');
    }

    // Funcio per esborrar el compte de l'usuari actual
    public function esborrarCompte()
    {
        $usuari = Usuari::find(Auth::user()->email);


        Auth::logout();

        // Esborrem la imatge de perfil de l'usuari
        if (Str::contains($usuari->avatar, ['default', 'google']) == false) {
            $nomImatge = explode('/', $usuari->avatar);
            unlink(storage_path('app/public/avatars/' . end($nomImatge) ));
        }

        // Esborrem tots els articles de l'usuari
        ArticleController::destroyAll($usuari->email);

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
                    'password.min' => 'La contrasenya ha de tenir com a mínim 6 caràcters, un número, una lletra majúscula i una minúscula',
                    'password.max' => 'La contrasenya ha de tenir com a màxim 25 caràcters',
                    'password.confirmed' => 'Les contrasenyes no coincideixen',
                    'password.regex' => 'La contrasenya ha de tenir com a mínim 6 caràcters, un número, una lletra majúscula i una minúscula',
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
                    'password' => 'required',
                    'g-token' => session('loginIntents') >= 3 ? 'required' : ''
                ],
                [
                    'emailUsername.required' => 'El email o el nom de usuari és obligatori',
                    'password.required' => 'La contrasenya és obligatòria',
                    'g-token.required' => 'El captcha és obligatori',
                ]
            );

            // Comprovem si la contrasenya és correcta utilitzant el hash check
            $usuari = Usuari::where('email', $request->emailUsername)->orWhere('username', $request->emailUsername)->first();

            if (!$usuari) {
                return redirect()->back()->withErrors(['error' => 'El correu o nom de usuari no són correctes'], 'login')->withInput();
            }

            if (Hash::check($request->password, $usuari->password) == false) {
                // Si la contrasenya esta malament, sumem un intent de login
                if (!session('loginIntents')) session(['loginIntents' => 1]);

                session(['loginIntents' => session('loginIntents') + 1]);

                return redirect()->back()->withErrors(['error' => 'La contrasenya no és correcta'], 'login')->withInput();
            }

            // Autenticar l'usuari
            Auth::login($usuari);

            // Redirigir l'usuari a la pàgina home
            return redirect()->route('home');
        } catch (ValidationException $e) {
            // Retornem la resposta error si hi ha hagut algun error de validació. 
            return redirect()->back()->withErrors($e->validator->getMessageBag(), 'login')->withInput();
        } catch (\Exception $e) {
            //Retornem la resposta error si ha ocorregut algun error
            return redirect()->back()->withErrors(['error' => 'Error al iniciar sessió'], 'login')->withInput();
        }
    }

    // Funcio per recuperar la contrasenya de l'usuari
    public function recuperar(Request $request)
    {
        try {
            // Validar les dades del formulari
            $request->validate(
                [
                    'email' => 'required|email'
                ],
                [
                    'email.required' => 'El email és obligatori',
                    'email.email' => 'El email no és vàlid'
                ]
            );

            // Comprovar si l'usuari existeix
            $usuari = Usuari::where('email', $request->email)->first();

            if (!$usuari) {
                return redirect()->back()->withErrors(['error' => 'Aquest email no està registrat'], 'recuperar')->withInput();
            }

            // Comprovem que l'usuari no sigui un usuari de Oauth
            if (!$usuari->password) {
                return redirect()->back()->withErrors(['error' => "Aquest compte no té disponible l'opció de recuperar contrasenya"], 'recuperar')->withInput();
            }

            // Generar un token per a la recuperació de la contrasenya, i el guardarem a la taula de password_reset_tokens
            $token = Password::createToken($usuari);

            $usuari->sendPasswordResetNotification($token);

            // Redirigir l'usuari a la pàgina de login
            return redirect()->back()->with('success', "S'ha enviat un correu per a recuperar la contrasenya.");
        } catch (ValidationException $e) {
            // Retornem la resposta error si hi ha hagut algun error de validació
            return redirect()->back()->withErrors($e->validator->getMessageBag(), 'recuperar')->withInput();
        } catch (\Exception $e) {
            //Retornem la resposta error si ha ocorregut algun error
            return redirect()->back()->withErrors(['error' => 'Error al recuperar la contrasenya' . ' ' . $e], 'recuperar')->withInput();
        }
    }

    // Funcio per restaurar la contrasenya de l'usuari
    public function restaurarForm(Request $request)
    {
        try {

            return view('restaurar', ['token' => $request->token]);
        } catch (\Exception $e) {
            //Retornem la resposta error si ha ocorregut algun error
            return redirect()->back()->withErrors(['error' => 'Error al restaurar la contrasenya'], 'restaurar')->withInput();
        }
    }

    // Funcio per restaurar la contrasenya de l'usuari

    public function restaurarContrasenya(Request $request)
    {
        try {
            // Validar les dades del formulari
            $request->validate(
                [
                    'email' => 'required|email',
                    'password' => 'required|min:6|max:25|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/|confirmed',
                    'token' => 'required'
                ],
                [
                    'email.required' => 'El email és obligatori',
                    'email.email' => 'El email no és vàlid',
                    'password.required' => 'La contrasenya és obligatòria',
                    'password.min' => 'La contrasenya ha de tenir com a mínim 6 caràcters, un número, una lletra majúscula i una minúscula',
                    'password.max' => 'La contrasenya ha de tenir com a màxim 25 caràcters',
                    'password.confirmed' => 'Les contrasenyes no coincideixen',
                    'password.regex' => 'La contrasenya ha de tenir com a mínim 6 caràcters, un número, una lletra majúscula i una minúscula',
                    'token.required' => 'El token és obligatori'
                ]
            );

            $credentials = $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            );

            $status = Password::reset($credentials, function ($user, $password) {
                $user->password = $password;
                $user->save();
            });

            // Comprovem si s'ha restaurat la contrasenya correctament
            if ($status == Password::PASSWORD_RESET) {
                // Si ha anat bé, redirigim l'usuari a la pàgina de login
                return redirect()->route('login')->with('success', 'Contrasenya restaurada correctament. Inicia sessió per continuar.');
            } else {

                // Comprovem si l'error és per un email incorrecte
                if ($status == Password::INVALID_USER) {
                    return back()->withInput()->withErrors(['error' => ['Aquest email no és correcte']], 'restaurar');
                }

                // Comprovem si l'error és per un token incorrecte
                if ($status == Password::INVALID_TOKEN) {
                    return back()->withInput()->withErrors(['error' => ['El token no és vàlid, reinicia el procés de recuperació de contrasenya']], 'restaurar');
                }
                // Comprovem si l'error és per un altre motiu
                return back()->withInput()->withErrors(['error' => ["No s'ha pogut restaurar la contrasenya"],], 'restaurar');
            }
        } catch (ValidationException $e) {
            // Retornem la resposta error si hi ha hagut algun error de validació
            return redirect()->back()->withErrors($e->validator->getMessageBag(), 'restaurar')->withInput();
        } catch (\Exception $e) {
            //Retornem la resposta error si ha ocorregut algun error
            return redirect()->back()->withErrors(['error' => 'Error al restaurar la contrasenya'], 'restaurar')->withInput();
        }
    }

    // Mostra la pàgina de configuracio de l'usuari actual
    public function configuracio()
    {
        return view('configuracio');
    }

    // Funcio per actualitzar el nom de usuari de l'usuari actual
    public function updateUsername(Request $request)
    {
        try {
            // Validar les dades del formulari
            $request->validate(
                [
                    'username' => 'required|unique:usuaris'
                ],
                [
                    'username.required' => 'El nom de usuari és obligatori',
                    'username.unique' => 'Aquest nom de usuari ja està registrat'
                ]
            );

            // Actualitzar el nom de usuari de l'usuari actual
            $usuari = Usuari::find(Auth::user()->email);
            $usuari->username = $request->username;
            $usuari->save();

            // Redirigir l'usuari a la pàgina de configuracio
            return redirect()->route('configuracio')->with('success', 'Nom de usuari actualitzat correctament');
        } catch (ValidationException $e) {
            // Retornem la resposta error si hi ha hagut algun error de validació
            return redirect()->back()->withErrors($e->validator->getMessageBag(), 'username')->withInput();
        } catch (\Exception $e) {
            //Retornem la resposta error si ha ocorregut algun error
            return redirect()->back()->with('error', 'Error al actualitzar el nom de usuari')->withInput();
        }
    }

    // Funcio per actualitzar la password de l'usuari actual
    public function updatePassword(Request $request)
    {
        try {
            // Validar les dades del formulari
            $request->validate(
                [
                    'password' => 'required|min:6|max:25|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/|confirmed'
                ],
                [
                    'password.required' => 'La contrasenya és obligatòria',
                    'password.min' => 'La contrasenya ha de tenir com a mínim 6 caràcters',
                    'password.max' => 'La contrasenya ha de tenir com a màxim 25 caràcters',
                    'password.confirmed' => 'Les contrasenyes no coincideixen',
                    'password.regex' => 'La contrasenya ha de contenir com a mínim una lletra majúscula, una minúscula i un número'
                ]
            );

            $usuari = Usuari::find(Auth::user()->email);

            // Comprovar si es un usuari de Oauth
            if (!$usuari->password) {
                return redirect()->back()>with('error', 'jkas');;
            }
            
            // Comprovar si la contrasenya antiga és correcta
            if (!Hash::check($request->old_password, $usuari->password)) {
                return redirect()->back()->withErrors(['old_password' => 'La contrasenya antiga no és correcta'], 'password');
            }

            // Actualitzar la contrasenya de l'usuari actual
            $usuari->password = $request->password;
            $usuari->save();

            // Redirigir l'usuari a la pàgina de configuracio
            return redirect()->route('configuracio')->with('success', 'Contrasenya actualitzada correctament');
        } catch (ValidationException $e) {
            // Retornem la resposta error si hi ha hagut algun error de validació
            return redirect()->back()->withErrors($e->validator->getMessageBag(), 'password');
        } catch (\Exception $e) {
            //Retornem la resposta error si ha ocorregut algun error
            return redirect()->back()->with('error', 'Error al actualitzar la contrasenya');
        }
    }

    // Funcio per actualitzar la imatge de perfil de l'usuari actual
    public function updateAvatar(Request $request)
    {
        try {
            // Validar les dades del formulari
            $request->validate(
                [
                    'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
                ],
                [
                    'avatar.required' => 'La imatge és obligatòria',
                    'avatar.image' => 'El fitxer ha de ser una imatge',
                    'avatar.mimes' => 'El fitxer ha de ser una imatge de tipus jpeg, png, jpg o gif',
                    'avatar.max' => 'La imatge ha de pesar com a màxim 2MB'
                ]
            );

            $usuari = Usuari::find(Auth::user()->email);

            // Si l'usuari ja té una imatge de perfil i no es la default, l'esborrem
            if (Str::contains($usuari->avatar, ['default', 'google']) == false) {
                $nomImatge = explode('/', $usuari->avatar);
                unlink(storage_path('app/public/avatars/' . end($nomImatge) ));
            }

            // Guardar la imatge de perfil de l'usuari actual a la carpeta publica
            $usuari->avatar = env('APP_URL') ."/". "storage/" . $request->file('avatar')->store('avatars', 'public');
            $usuari->save();



            // Redirigir l'usuari a la pàgina de configuracio
            return redirect()->route('configuracio')->with('success', 'Imatge de perfil actualitzada correctament');
        } catch (ValidationException $e) {
            // Retornem la resposta error si hi ha hagut algun error de validació
            return redirect()->back()->withErrors($e->validator->getMessageBag(), 'avatar');
        } catch (\Exception $e) {
            //Retornem la resposta error si ha ocorregut algun error
            return redirect()->back()->with('error', 'Error al actualitzar la imatge de perfil' . $e);
        }
    }
    
}
