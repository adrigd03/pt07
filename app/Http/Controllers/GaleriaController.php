<?php

namespace App\Http\Controllers;
use App\Models\Imatge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GaleriaController extends Controller
{
    /**
     * Mostra la galeria de l'usuari llogat
     * @return view galeria
     */
    public function galeria()
    {
        try{
            // Agafem totes les imatges de l'usuari llogat
            $imatges = Imatge::where('usuari', Auth::user()->email)->paginate(5);
            return view('galeria', compact('imatges'));
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Error al carregar la galeria.');
        }
        
    }

    /**
     * Funció per crear una imatge
     * @param Request $request dades de la imatge
     * @return redirecció a la galeria
     * @throws \Exception si no es pot crear la imatge
     * @throws ValidationException si hi ha errors en les dades de la imatge
     */
    public function crear(Request $request){
        try{
            $request->validate([
                'titol' => 'required|string|min:3|max:50',
                'descripcio' => 'required|string|min:10|max:255',
                'imatge' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],[
                'titol.required' => 'El camp titol és obligatori.',
                'titol.min' => 'El camp titol ha de tenir com a mínim 3 caràcters.',
                'titol.max' => 'El camp titol ha de tenir com a màxim 50 caràcters.',
                'titol.string' => 'El camp titol ha de ser un text.',
                'descripcio.required' => 'El camp descripcio és obligatori.',
                'descripcio.min' => 'El camp descripcio ha de tenir com a mínim 10 caràcters.',
                'descripcio.max' => 'El camp descripcio ha de tenir com a màxim 255 caràcters.',
                'descripcio.string' => 'El camp descripcio ha de ser un text.',
                'imatge.required' => 'El camp imatge és obligatori.',
                'imatge.image' => 'El camp imatge ha de ser una imatge.',
                'imatge.mimes' => 'El camp imatge ha de ser una imatge de tipus: jpeg, png, jpg, gif, svg.',
                'imatge.max' => 'El camp imatge ha de ser una imatge de màxim 2MB.',
            ]);
            // Guardem la imatge
            $imatge = new Imatge();
            $imatge->titol = $request->titol;
            $imatge->descripcio = $request->descripcio;
            $imatge->usuari = Auth::user()->email;
            $imatge->url = env('APP_URL') ."/". "storage/" . $request->file('imatge')->store('imatges', 'public');
            $imatge->save();

            return redirect()->back()->with('success', 'Imatge afegida correctament.');

        }catch(ValidationException $e){
            return redirect()->back()->withErrors($e->validator->getMessageBag(), 'crearImatge')->withInput();
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Error al crear la imatge.');
        }

    }

    /**
     * Funció per eliminar una imatge
     * @param $id id de la imatge
     * @return redirecció a la galeria
     * @throws \Exception si no es pot eliminar la imatge
     */
    public function destroy($id){
        try{
            $imatge = Imatge::find($id);

            // Comprovem que la imatge sigui de l'usuari
            if($imatge->usuari != Auth::user()->email){
                return redirect()->back()->with('error', 'No pots eliminar una imatge que no és teva.');
            }

            // Esborrem la imatge 
            $nomImatge = explode('/', $imatge->url);
            unlink(storage_path('app/public/imatges/' . end($nomImatge)));
            $imatge->delete();

            return redirect()->back()->with('success', 'Imatge eliminada correctament.');
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Error al eliminar la imatge.');
        }
    }

    /**
     * Funció per eliminar totes les imatges d'un usuari
     * @param $email email de l'usuari
     * @return true|false si s'han eliminat totes les imatges, false si no s'han pogut eliminar
     * @throws \Exception si no es poden eliminar les imatges
     */
    public static function destroyAll($email){
        try{
            $imatges = Imatge::where('usuari', $email)->get();
            // Esborrem totes les imatges de l'usuari
            foreach($imatges as $imatge){
                $nomImatge = explode('/', $imatge->url);
                unlink(storage_path('app/public/imatges/' . end($nomImatge)));
                $imatge->delete();
            }
            // Retornem true si s'han eliminat totes les imatges
            return true;
        }catch(\Exception $e){
            return false;
        }
    }

    /**
     * Funció per editar una imatge
     * @param Request $request dades de la imatge
     * @param $id id de la imatge
     * @return redirecció a la galeria
     * @throws \Exception si no es pot editar la imatge
     * @throws ValidationException si hi ha errors en les dades de la imatge
     */
    public  function editar(Request $request, $id){
        try{
            $request->validate([
                'titol' => 'required|string|min:3|max:50',
                'descripcio' => 'required|string|min:10|max:255',
            ],[
                'titol.required' => 'El camp titol és obligatori.',
                'titol.min' => 'El camp titol ha de tenir com a mínim 3 caràcters.',
                'titol.max' => 'El camp titol ha de tenir com a màxim 50 caràcters.',
                'titol.string' => 'El camp titol ha de ser un text.',
                'descripcio.required' => 'El camp descripcio és obligatori.',
                'descripcio.min' => 'El camp descripcio ha de tenir com a mínim 10 caràcters.',
                'descripcio.max' => 'El camp descripcio ha de tenir com a màxim 255 caràcters.',
                'descripcio.string' => 'El camp descripcio ha de ser un text.',
            ]);

            $imatge = Imatge::find($id);
            
            // Comprovem que l'imatge existeixi
            if(!$imatge){
                return redirect()->back()->with('error', 'No s\'ha trobat la imatge.');
            }

            // Comprovem que la imatge sigui de l'usuari
            if($imatge->usuari != Auth::user()->email){
                return redirect()->back()->with('error', 'No pots editar una imatge que no és teva.');
            }

            // Editem la imatge
            $imatge->titol = $request->titol;
            $imatge->descripcio = $request->descripcio;
            $imatge->save();

            return redirect()->back()->with('success', 'Imatge editada correctament.');
        }catch(ValidationException $e){
            return redirect()->back()->withErrors($e->validator->getMessageBag(), 'editarImatge')->with('idImatge', $id)->withInput();
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Error al editar la imatge.');
        }
    }
}
