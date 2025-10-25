<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('usuarios')->insert([
            [
                'nombre_usuario' => 'Admin General',
                'correo' => 'admin@eco.com',
                'contrasena' => Hash::make('admin123'),
                'rol' => 'admin',
            ],
            [
                'nombre_usuario' => 'Juan Pérez',
                'correo' => 'recolector1@eco.com',
                'contrasena' => Hash::make('reco123'),
                'rol' => 'recolector',
            ],
            [
                'nombre_usuario' => 'Lucía Torres',
                'correo' => 'vecina1@eco.com',
                'contrasena' => Hash::make('vecina123'),
                'rol' => 'vecino',
            ],
        ]);
    }
}
