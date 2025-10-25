<?php

namespace App\Http\Controllers;

use App\Models\TipoResiduo;
use Illuminate\Http\Request;

class ResiduoController extends Controller
{
    public function index()
    {
        $tipos = TipoResiduo::all();
        return view('residuos.index', compact('tipos'));
    }

    public function create()
    {
        return view('residuos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:100',
            'categoria' => 'required|in:orgánico,inorgánico,reciclable,peligroso',
        ]);

        TipoResiduo::create($request->all());
        return redirect()->route('residuos.index')->with('success', 'Tipo de residuo creado');
    }

    public function edit(TipoResiduo $residuo)
    {
        return view('residuos.edit', compact('residuo'));
    }

    public function update(Request $request, TipoResiduo $residuo)
    {
        $request->validate([
            'nombre' => 'required|max:100',
            'categoria' => 'required|in:orgánico,inorgánico,reciclable,peligroso',
        ]);

        $residuo->update($request->all());
        return redirect()->route('residuos.index')->with('success', 'Tipo de residuo actualizado');
    }

    public function destroy(TipoResiduo $residuo)
    {
        $residuo->delete();
        return redirect()->route('residuos.index')->with('success', 'Tipo de residuo eliminado');
    }
}
