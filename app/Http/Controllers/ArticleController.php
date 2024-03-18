<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Imatge;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ArticleController extends Controller
{
    /**
     * Mostra la pàgina principal amb tots els articles
     * @return view home
     */
    public function home()
    {
        // Agafem tots els articles i els paginem 
        $articles = Article::latest()->paginate(5);

        // Comrpovem si l'usuari està autenticat
        if (Auth::check()) {
            // Agafem totes les imatges de l'usuari llogat
            $userImatges = Imatge::where('usuari', Auth::user()->email)->get();
            return view('home', compact('articles', 'userImatges'));
        } else {
            return view('home', compact('articles'));
        }
    }

    /**
     * Funció per crear un article
     * @param Request $request dades de l'article
     * @return redirecció a la pàgina anterior
     * @throws \Exception si no es pot crear l'article
     * @throws ValidationException si hi ha errors en les dades de l'article
     */
    public function create(Request $request)
    {
        try {
            $request->validate(
                [
                    'titol' => 'required|string|min:3|max:50',
                    'contingut' => 'required|string|min:10|max:255',
                ],
                [
                    'titol.required' => 'El camp titol és obligatori.',
                    'titol.min' => 'El camp titol ha de tenir com a mínim 3 caràcters.',
                    'titol.max' => 'El camp titol ha de tenir com a màxim 50 caràcters.',
                    'titol.string' => 'El camp titol ha de ser un text.',
                    'contingut.required' => 'El camp contingut és obligatori.',
                    'contingut.min' => 'El camp contingut ha de tenir com a mínim 10 caràcters.',
                    'contingut.max' => 'El camp contingut ha de tenir com a màxim 255 caràcters.',
                    'contingut.string' => 'El camp contingut ha de ser un text.',


                ]
            );

            
            
            $article = new Article();
            $article->titol = $request->titol;
            $article->contingut = $request->contingut;
            $article->usuari = $request->user()->email;

            // Comprovem que la imatge existeix
            $imatge = Imatge::find($request->imatge);
            if($imatge){
                // Comprovem que la imatge pertany a l'usuari
                if ($imatge->usuari != Auth::user()->email) {
                    return redirect()->back()->withErrors(['imatge' => 'La imatge no existeix a a la teva galeria.'], 'crearArticle')->withInput();
                }
                // Guardem la imatge
                $article->imatge = $imatge->url;
            }

            // Guardem l'article
            $article->save();

            return redirect()->back()->with('success', 'Article creat correctament.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator->getMessageBag(), 'crearArticle')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear l\'article.');
        }
    }

    /**
     * Funció per editar un article
     * @param Request $request dades de l'article
     * @param $id id de l'article
     * @return redirecció a la pàgina anterior
     * @throws \Exception si no es pot editar l'article
     * @throws ValidationException si hi ha errors en les dades de l'article
     */
    public function editar(Request $request, $id)
    {
        try {
            $request->validate(
                [
                    'titol' => 'required|string|min:3|max:50',
                    'contingut' => 'required|string|min:10|max:255',
                ],
                [
                    'titol.required' => 'El camp titol és obligatori.',
                    'contingut.required' => 'El camp contingut és obligatori.',

                ]
            );
            // Agafem l'article
            $article = Article::find($id);

            // Comprovem que l'article existeix
            if (!$article) {
                return redirect()->back()
                    ->with('error', 'No s\'ha trobat l\'article.');
            }

            // Comprovem que l'article sigui de l'usuari
            if (Auth::user()->email != $article->usuari) {
                return redirect()->back()
                    ->with('error', 'No tens permisos per editar aquest article.');
            }

            $article->titol = $request->titol;
            $article->contingut = $request->contingut;
            // Comprovem que la imatge existeix
            $imatge = Imatge::find($request->imatge);
            if($imatge){
                // Comprovem que la imatge pertany a l'usuari
                if ($imatge->usuari != Auth::user()->email) {
                    return redirect()->back()->withErrors(['imatge' => 'La imatge no existeix a a la teva galeria.'], 'editarArticle')->with('idArticle', $id)->withInput();
                }
                // Guardem la imatge
                $article->imatge = $imatge->url;
            }
            // Guardem l'article
            $article->save();

            return redirect()->back()->with('success', 'Article actualitzat correctament.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator->getMessageBag(), 'editarArticle')->with('idArticle', $id)->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al editar l\'article.');
        }
    }

    /**
     * Funció per esborrar un article
     * @param $id id de l'article
     * @return redirecció a la pàgina anterior
     * @throws \Exception si no es pot esborrar l'article
     * @throws ValidationException si hi ha errors en les dades de l'article
     */
    public function destroy($id)
    {
        // Agafem l'article
        $article = Article::find($id);
        
        // Comprovem que l'article existeix
        if (!$article) {
            return redirect()->back()
                ->with('error', 'No s\'ha trobat l\'article.');
        }

        // Comprovem que l'article sigui de l'usuari
        if (Auth::user()->email != $article->usuari) {
            return redirect()->back()
                ->with('error', 'No tens permisos per esborrar aquest article.');
        }

        // Esborrem l'article
        $article->delete();

        return redirect()->back()->with('success', 'Article eliminat correctament.');
    }

    /**
     * Funció per esborrar tots els articles d'un usuari
     * @param $email email de l'usuari
     * @return true|false si s'han eliminat tots els articles, false si no s'han pogut eliminar
     * @throws \Exception si no es poden eliminar els articles 
     */
    public static function destroyAll($email)
    {
        try {
            // Agafem tots els articles de l'usuari
            $articles = Article::where('usuari', $email)->get();
            
            // Esborrem tots els articles de l'usuari
            foreach ($articles as $article) {
                $article->delete();
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
       
    }

    /**
     * Mostra els articles de l'usuari llogat
     * @return view articlesPropis
     */
    public function articlesPropis()
    {
        // Agafem tots els articles de l'usuari llogat
        $articles = Article::where('usuari', Auth::user()->email)->latest()->paginate(5);
        // Agafem totes les imatges de l'usuari llogat
        $userImatges = Imatge::where('usuari', Auth::user()->email)->get();

        return view('articlesPropis', compact('articles', 'userImatges'));
    }
}
