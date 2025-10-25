<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    use HasFactory;

    protected $table = 'rutas';
    protected $primaryKey = 'id_ruta';
    public $timestamps = false;

    protected $fillable = [
        'nombre_ruta',
        'zona',
        'id_recolector',
    ];

    public function recolector()
    {
        return $this->belongsTo(Usuario::class, 'id_recolector', 'Id_usuario');
    }
}
