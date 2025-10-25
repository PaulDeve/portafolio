<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';
    protected $primaryKey = 'id_pago';
    public $timestamps = false;

    protected $fillable = [
        'id_cobranza',
        'tipo_pago',
        'monto',
        'fecha_pago',
    ];

    public function cobranza()
    {
        return $this->belongsTo(Cobranza::class, 'id_cobranza', 'Id_codigo');
    }
}
