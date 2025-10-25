<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VecinoMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if(session('usuario_rol') !== 'vecino'){
            abort(403, 'Acceso denegado');
        }
        return $next($request);
    }
}
