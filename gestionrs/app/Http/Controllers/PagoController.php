<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Cobranza;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function index()
    {
        $pagos = Pago::with('cobranza')->get();
        return view('pagos.index', compact('pagos'));
    }

    public function create()
    {
        $cobranzas = Cobranza::all();
        return view('pagos.create', compact('cobranzas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cobranza' => 'required|exists:cobranza,Id_codigo',
            'tipo_pago' => 'required|in:efectivo,tarjeta,transferencia',
            'monto' => 'required|numeric',
        ]);

        Pago::create($request->all());
        return redirect()->route('pagos.index')->with('success', 'Pago registrado');
    }
}
