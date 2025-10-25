<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::all();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_usuario' => 'required|max:50',
            'correo' => 'required|email|unique:usuarios,correo',
            'contrasena' => 'required|min:6',
            'rol' => 'required|in:admin,recolector,vecino',
        ]);

        Usuario::create([
            'nombre_usuario' => $request->nombre_usuario,
            'correo' => $request->correo,
            'contrasena' => bcrypt($request->contrasena),
            'rol' => $request->rol,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado');
    }

    public function edit(Usuario $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'nombre_usuario' => 'required|max:50',
            'correo' => 'required|email|unique:usuarios,correo,'.$usuario->Id_usuario.',Id_usuario',
            'rol' => 'required|in:admin,recolector,vecino',
        ]);

        $usuario->update(
            array_filter([
                'nombre_usuario' => $request->nombre_usuario,
                'correo' => $request->correo,
                'rol' => $request->rol,
                'contrasena' => $request->contrasena ? bcrypt($request->contrasena) : null,
            ])
        );

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado');
    }

    public function destroy(Usuario $usuario)
    {
        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado');
    }
}
