<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cobranza;
use App\Models\TipoResiduo;

class ReporteController extends Controller
{
    public function index()
    {
        // Datos de ejemplo para gráficas
        $porTipo = TipoResiduo::selectRaw('categoria, count(*) as total')->groupBy('categoria')->get();
        $datos = [
            'porTipo' => $porTipo,
        ];
        return view('reportes.index', $datos);
    }

    // Métodos para exportar PDF/Excel se agregarán luego
}
