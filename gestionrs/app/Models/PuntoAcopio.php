<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuntoAcopio extends Model
{
    use HasFactory;

    protected $table = 'puntos_acopio';
    protected $primaryKey = 'id_punto';
    public $timestamps = false;

    protected $fillable = [
        'nombre_punto',
        'direccion',
        'capacidad_max',
        'encargado',
    ];
}
