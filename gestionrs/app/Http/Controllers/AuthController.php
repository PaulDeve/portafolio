<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'correo' => ['required','email'],
            'contrasena' => ['required'],
        ]);

        // Intentar autenticación personalizada
        $user = Usuario::where('correo', $credentials['correo'])->first();
        if($user && password_verify($credentials['contrasena'], $user->contrasena)){
            // Login manual (sin usar guard) - usar session
            session(['usuario_id' => $user->Id_usuario, 'usuario_rol' => $user->rol]);
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['correo' => 'Credenciales inválidas']);
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }
}
