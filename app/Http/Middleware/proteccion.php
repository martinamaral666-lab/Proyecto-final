<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class proteccion
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->rol !== 'empleado') {
            abort(403, 'No tienes permisos para acceder a esta página.');
        }

        return $next($request);
    }
}
