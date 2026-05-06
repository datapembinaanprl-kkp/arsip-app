<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsDirektur
{
    /**
     * Hanya direktur yang bisa mengakses kanban view.
     * Staff tetap bisa update status dokumennya sendiri via route terpisah.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->hasRole('direktur')) {
            abort(403, 'Halaman ini hanya dapat diakses oleh Direktur.');
        }

        return $next($request);
    }
}