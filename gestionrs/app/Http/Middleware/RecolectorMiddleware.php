<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RecolectorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if(session('usuario_rol') !== 'recolector'){
            abort(403, 'Acceso denegado');
        }
        return $next($request);
    }
}
