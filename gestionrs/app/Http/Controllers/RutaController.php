<?php

namespace App\Http\Controllers;

use App\Models\Ruta;
use App\Models\Usuario;
use Illuminate\Http\Request;

class RutaController extends Controller
{
    public function index()
    {
        $rutas = Ruta::with('recolector')->get();
        return view('rutas.index', compact('rutas'));
    }

    public function create()
    {
        $recolectores = Usuario::where('rol', 'recolector')->get();
        return view('rutas.create', compact('recolectores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_ruta' => 'required|max:100',
            'zona' => 'required|max:100',
            'id_recolector' => 'nullable|exists:usuarios,Id_usuario',
        ]);

        Ruta::create($request->all());
        return redirect()->route('rutas.index')->with('success', 'Ruta creada');
    }

    public function edit(Ruta $ruta)
    {
        $recolectores = Usuario::where('rol', 'recolector')->get();
        return view('rutas.edit', compact('ruta', 'recolectores'));
    }

    public function update(Request $request, Ruta $ruta)
    {
        $request->validate([
            'nombre_ruta' => 'required|max:100',
            'zona' => 'required|max:100',
            'id_recolector' => 'nullable|exists:usuarios,Id_usuario',
        ]);

        $ruta->update($request->all());
        return redirect()->route('rutas.index')->with('success', 'Ruta actualizada');
    }

    public function destroy(Ruta $ruta)
    {
        $ruta->delete();
        return redirect()->route('rutas.index')->with('success', 'Ruta eliminada');
    }
}
