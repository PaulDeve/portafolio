<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cobranza extends Model
{
    use HasFactory;

    protected $table = 'cobranza';
    protected $primaryKey = 'Id_codigo';
    public $timestamps = false;

    protected $fillable = [
        'Concepto',
        'Fecha_hora',
        'Cantidad',
        'Precio_unitario',
        'Id_usuario',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'Id_usuario', 'Id_usuario');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_cobranza', 'Id_codigo');
    }
}
