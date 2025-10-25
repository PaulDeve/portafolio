<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cobranza;
use App\Models\Ruta;
use App\Models\Usuario;

class DashboardController extends Controller
{
    public function index()
    {
        $totalResiduos = Cobranza::sum('Cantidad');
        $totalRutas = Ruta::count();
        $pagosRealizados = \App\Models\Pago::sum('monto');
        $usuariosRegistrados = Usuario::count();

        return view('dashboard', compact('totalResiduos','totalRutas','pagosRealizados','usuariosRegistrados'));
    }
}
