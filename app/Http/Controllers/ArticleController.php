<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ArticleController extends Controller
{

    public function home()
    {
        $articles = Article::latest()->paginate(5);
        return view('home', compact('articles'));
    }




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
                    'contingut.required' => 'El camp contingut és obligatori.',

                ]
            );


            $article = new Article();
            $article->titol = $request->titol;
            $article->contingut = $request->contingut;
            $article->usuari = $request->user()->email;
            $article->save();

            return redirect()->back()
                ->with('success', 'Article creat correctament.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator->getMessageBag(), 'crearArticle')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear l\'article.');
        }
    }


    

    public function editar(Request $request, $id)
    {
        try {
            $request->validate( [
                'titol' => 'required|string|min:3|max:50',
                'contingut' => 'required|string|min:10|max:255',
            ],
            [
                'titol.required' => 'El camp titol és obligatori.',
                'contingut.required' => 'El camp contingut és obligatori.',

            ]);

            $article = Article::find($id);

            if(!$article){
                return redirect()->back()
                ->with('error', 'No s\'ha trobat l\'article.');
            }

            if(Auth::user()->email != $article->usuari ){
                return redirect()->back()
                ->with('error', 'No tens permisos per editar aquest article.');
            }
            $article->titol = $request->titol;
            $article->contingut = $request->contingut;
            $article->save();

            return redirect()->back()->with('success', 'Article actualitzat correctament.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator->getMessageBag(), 'editarArticle')->with('idArticle',$id)->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al editar l\'article.');
        }
    }

    public function destroy($id)
    {
        $article = Article::find($id);

        if(!$article){
            return redirect()->back()
            ->with('error', 'No s\'ha trobat l\'article.');
        }

        if(Auth::user()->email != $article->usuari ){
            return redirect()->back()
            ->with('error', 'No tens permisos per esborrar aquest article.');
        }

        $article->delete();

        return redirect()->back()
            ->with('success', 'Article eliminat correctament.');
    }
}
