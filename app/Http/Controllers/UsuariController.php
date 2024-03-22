<?php

namespace App\Http\Controllers;

use App\Models\Usuari;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\ArticleController;

class UsuariController extends Controller
{
    /**
     * Redirigir l'usuari a la pàgina de login de Google
     * @return Socialite
     */
    public function loginGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Callback de Google per obtenir les dades de l'usuari
     * @param Request $request dades de l'usuari
     * @return redirecció a la pàgina home si l'usuari s'ha autenticat correctament, altrament redirigir a la pàgina de login
     * @throws \Exception si no es poden obtenir les dades de l'usuari
     */
    public function googleCallback(Request $request)
    {

        try {
            // Obtenir les dades de l'usuari
            $user = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error al obtenir les dades del usuari.');
        }


        //verificar si el usuario existe en la base de datos y si no existe crearlo y loguearlo
        $usuari = Usuari::find($user->email);

        // Si l'usuari no existeix, el creem
        if (!$usuari) {
            $usuari = new Usuari();
            $usuari->email = $user->email;
            $usuari->avatar = $user->avatar;

            // Si l'usuari te nickname, el guardem com a username, altrament guardem el primer nom de l'usuari
            if ($user->nickname != null) {
                // Comprovem si el nickname ja existeix, si existeix afegim un número al final i el guardem
                $user->username = Usuari::getAvailableNickname($user->nickname);
            } else {

                // Si l'usuari no te nickname, guardem el primer nom de l'usuari com a username
                $usuari->username = strtolower(explode(' ', $user->name)[0]);
                $usuari->username = Usuari::getAvailableNickname($usuari->username);
            }

            // Guardem el usuari
            $usuari->save();
        }

        // Autenticar l'usuari
        Auth::login($usuari);

        return redirect()->route('home');
    }

    /**
     * Funció per tancar la sessió de l'usuari
     * @return redirecció a la pàgina home
     */
    public function logout()
    {
        try{

            // Tancar la sessió de l'usuari
            Auth::logout();
            
            // Esborrar totes les dades de la sessió
            session()->flush();
            
            return redirect()->route('home');
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Error al tancar la sessió');
        }
    }

    /**
     * Funció per esborrar el compte de l'usuari
     * @return redirecció a la pàgina home si l'usuari s'ha esborrat correctament, altrament redirigir a la pàgina de configuració
     * @throws \Exception si no es pot esborrar l'usuari
     */
    public function esborrarCompte()
    {
        try {
            // Obtenir l'usuari
            $usuari = Usuari::find(Auth::user()->email);

            // Comprovar si l'usuari existeix
            if (!$usuari) {
                return redirect()->back()->with('error', 'No s\'ha trobat l\'usuari');
            }

            // Esborrem totes les imatges de l'usuari
            if(GaleriaController::destroyAll($usuari->email) == false){
                return redirect()->back()->with('error', 'Error al esborrar la galeria');
            }

            
            // Esborrem tots els articles de l'usuari
            if(ArticleController::destroyAll($usuari->email) == false){
                return redirect()->back()->with('error', 'Error al esborrar els articles');
            }

            // Esborrem la imatge de perfil de l'usuari
            if (Str::contains($usuari->avatar, ['default', 'google']) == false) {
                $nomImatge = explode('/', $usuari->avatar);
                unlink(storage_path('app/public/avatars/' . end($nomImatge)));
            }

            // Tancar la sessió de l'usuari
            Auth::logout();

            // Esborrar l'usuari
            $usuari->delete();

            return redirect()->route('home');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al esborrar el compte');
        }
    }

    /**
     * Funció per registrar un usuari
     * @param Request $request dades de l'usuari
     * @return redirecció a la pàgina de login si l'usuari s'ha registrat correctament, altrament redirigir a la pàgina de registre
     * @throws \Exception si no es pot registrar l'usuari
     * @throws ValidationException si hi ha errors en les dades de l'usuari
     */
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

    /**
     * Funció per iniciar sessió
     * @param Request $request dades de l'usuari
     * @return redirecció a la pàgina home si l'usuari s'ha autenticat correctament, altrament redirigir a la pàgina de login
     * @throws \Exception si no es pot iniciar sessió
     * @throws ValidationException si hi ha errors en les dades de l'usuari
     */
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

            // Agafar l'usuari per email o username
            $usuari = Usuari::where('email', $request->emailUsername)->orWhere('username', $request->emailUsername)->first();

            // Comprovar si l'usuari existeix
            if (!$usuari) {
                return redirect()->back()->withErrors(['error' => 'El correu o nom de usuari no són correctes'], 'login')->withInput();
            }

            // Comprovem si la contrasenya es correcta
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
            return redirect()->back()->withErrors($e->validator->getMessageBag(), 'login')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al iniciar sessió'], 'login')->withInput();
        }
    }

    /**
     * Funció per enviar el email de recuperacio de la contrasenya a l'usuari
     * @param Request $request dades de l'usuari
     * @return redirecció a la pàgina de login amb success si a l'usuari se li ha enviat el correu correctament, altrament redirigir a la pàgina de login amb error
     * @throws \Exception si no es pot enviar el correu de recuperacio de la contrasenya
     * @throws ValidationException si hi ha errors en les dades de l'usuari
     */
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

            // Agafar l'usuari per email
            $usuari = Usuari::where('email', $request->email)->first();

            // Comprovar si l'usuari existeix
            if (!$usuari) {
                return redirect()->back()->withErrors(['error' => 'Aquest email no està registrat'], 'recuperar')->withInput();
            }

            // Comprovem que l'usuari no sigui un usuari de Oauth
            if (!$usuari->password) {
                return redirect()->back()->withErrors(['error' => "Aquest compte no té disponible l'opció de recuperar contrasenya"], 'recuperar')->withInput();
            }

            // Generar un token per a la recuperació de la contrasenya, i el guardarem a la taula de password_reset_tokens
            $token = Password::createToken($usuari);

            // Enviar el correu de recuperacio de la contrasenya a l'usuari
            $usuari->sendPasswordResetNotification($token);

            return redirect()->back()->with('success', "S'ha enviat un correu per a recuperar la contrasenya.");

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator->getMessageBag(), 'recuperar')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al recuperar la contrasenya' . ' ' . $e], 'recuperar')->withInput();
        }
    }

    /**
     * Mostra el formulari per restaurar la contrasenya
     * @param Request $request dades de l'usuari
     * @return redirecció a la pàgina de restaurar contrassenya si no hi ha cap error, altrament redirigir a la pàgina de login amb error
     * @throws \Exception si no es pot mostrar el formulari de restaurar contrasenya
     */
    public function restaurarForm(Request $request)
    {
        try {
            // Mostrar el formulari per restaurar la contrasenya i passar el token
            return view('restaurar', ['token' => $request->token]);
        } catch (\Exception $e) {
            //Retornem la resposta error si ha ocorregut algun error
            return redirect()->back()->withErrors(['error' => 'Error al restaurar la contrasenya'], 'restaurar')->withInput();
        }
    }

    /**
     * Funció per restaurar la contrasenya de l'usuari
     * @param Request $request dades de l'usuari
     * @return redirecció a la pàgina de login si s'ha restaurat la contrasenya correctament, altrament redirigir a la pàgina de restaurar contrassenya amb error
     * @throws \Exception si no es pot restaurar la contrasenya
     * @throws ValidationException si hi ha errors en les dades de l'usuari
     */
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

            // Crear un array amb les dades del usuari
            $credentials = $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            );

            // Restaurar la contrasenya de l'usuari
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

    /**
     * Mostra la pàgina de configuració de l'usuari
     * @return redirecció a la pàgina de configuració
     */
    public function configuracio()
    {
        try {
            // Mostrar la pàgina de configuració
            return view('configuracio');
        } catch (\Exception $e) {
            //Retornem la resposta error si ha ocorregut algun error
            return redirect()->back()->with('error', 'Error al mostrar la configuració');
        }
    }

    /**
     * Funció per actualitzar el nom de usuari de l'usuari actual
     * @param Request $request dades de l'usuari
     * @return redirecció a la pàgina de configuració si s'ha actualitzat el nom de usuari correctament, altrament redirigir a la pàgina de configuració amb error
     * @throws \Exception si no es pot actualitzar el nom de usuari
     * @throws ValidationException si hi ha errors en les dades de l'usuari
     */
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

            // Agafar l'usuari actual
            $usuari = Usuari::find(Auth::user()->email);

            // Actualitzar el nom de usuari de l'usuari actual
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

    /**
     * Funció per actualitzar la contrasenya de l'usuari actual
     * @param Request $request dades de l'usuari
     * @return redirecció a la pàgina de configuració si s'ha actualitzat la contrasenya correctament, altrament redirigir a la pàgina de configuració amb error
     * @throws \Exception si hi ha hagut algun error
     * @throws ValidationException si hi ha errors en les dades de l'usuari
     */
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

            // Agafar l'usuari actual
            $usuari = Usuari::find(Auth::user()->email);

            // Comprovar si es un usuari de Oauth
            if (!$usuari->password) {
                return redirect()->back() > with('error', 'jkas');;
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

    /**
     * Funció per actualitzar la imatge de perfil de l'usuari actual
     * @param Request $request dades de l'usuari
     * @return redirecció a la pàgina de configuració si s'ha actualitzat la imatge de perfil correctament, altrament redirigir a la pàgina de configuració amb error
     * @throws \Exception si hi ha hagut algun error
     * @throws ValidationException si hi ha errors en les dades de l'usuari
     */
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
            // Agafar l'usuari actual
            $usuari = Usuari::find(Auth::user()->email);

            // Si l'usuari ja té una imatge de perfil i no es la default, l'esborrem
            if (Str::contains($usuari->avatar, ['default', 'google']) == false) {
                $nomImatge = explode('/', $usuari->avatar);
                unlink(storage_path('app/public/avatars/' . end($nomImatge)));
            }

            // Guardar la imatge de perfil de l'usuari actual a la carpeta publica
            $usuari->avatar = env('APP_URL') . "/" . "storage/" . $request->file('avatar')->store('avatars', 'public');
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
