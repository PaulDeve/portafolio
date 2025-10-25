<?php

namespace App\Http\Controllers;

use App\Models\Cobranza;
use App\Models\TipoResiduo;
use App\Models\Ruta;
use Illuminate\Http\Request;

class CobranzaController extends Controller
{
    public function index()
    {
        $cobranza = Cobranza::with('usuario')->get();
        return view('cobranza.index', compact('cobranza'));
    }

    public function create()
    {
        $rutas = Ruta::all();
        $tipos = TipoResiduo::all();
        return view('cobranza.create', compact('rutas', 'tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Concepto' => 'required|max:100',
            'Cantidad' => 'required|numeric',
            'Precio_unitario' => 'required|numeric',
            'Id_usuario' => 'required|exists:usuarios,Id_usuario',
        ]);

        Cobranza::create($request->all());
        return redirect()->route('cobranza.index')->with('success', 'Registro guardado');
    }
}
