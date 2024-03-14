<?php

namespace App\Http\Middleware;

use App\Models\Article;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedireccionarPaginaInvalida
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Agafem el numero de pàgina de la url
        $page = $request->query('page', 1);

        // Calculem el nombre de pàgines que hi ha de articles 
        $itemsPerPage = 5;
        $totalArticles = Article::count();
        $totalPages = ceil($totalArticles / $itemsPerPage);

         // Si la pàgina es major que el nombre de pàgines de articles redirigim a la pàgina 1
        if ($page > $totalPages && $totalPages > 0) {
            if($request->is('articles-propis')){
                return redirect()->route('articles-propis', ['page' => 1]);
            }
            return redirect()->route('home');
        }
        


        return $next($request);
    }
}
