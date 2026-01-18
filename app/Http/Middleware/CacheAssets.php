<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheAssets
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Solo cachear assets estáticos
        $path = $request->path();

        // Detectar si es un asset estático (JS, CSS, fonts, images)
        if (preg_match('/\.(js|css|woff2?|ttf|eot|svg|png|jpg|jpeg|gif|ico|webp)$/i', $path)) {
            // Cachear por 1 año para assets con hash (versionados por Vite)
            if (preg_match('/-[a-f0-9]{8,}\.(js|css)$/i', $path)) {
                $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            }
            // Cachear por 1 semana para otros assets
            else {
                $response->headers->set('Cache-Control', 'public, max-age=604800');
            }

            // Agregar headers adicionales de rendimiento
            $response->headers->set('X-Content-Type-Options', 'nosniff');
        }

        // Para páginas HTML, no cachear
        elseif (
            $response->headers->get('Content-Type') === 'text/html' ||
            $response->headers->get('Content-Type') === 'application/json'
        ) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        }

        return $response;
    }
}
