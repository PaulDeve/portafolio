<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    protected $primaryKey = 'Id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'nombre_usuario',
        'correo',
        'contrasena',
        'rol',
        'fecha_registro',
    ];

    protected $hidden = [
        'contrasena',
    ];

    // Relaciones
    public function rutas()
    {
        return $this->hasMany(Ruta::class, 'id_recolector', 'Id_usuario');
    }

    public function cobranzas()
    {
        return $this->hasMany(Cobranza::class, 'Id_usuario', 'Id_usuario');
    }
}
