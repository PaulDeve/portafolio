<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoResiduo extends Model
{
    use HasFactory;

    protected $table = 'tipos_residuo';
    protected $primaryKey = 'id_residuo';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'categoria',
    ];
}
