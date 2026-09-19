<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cobros extends Model
{
    /** @use HasFactory<\Database\Factories\CobrosFactory> */
    use HasFactory;

    protected $fillable = [
        'nombre_cliente',
        'telefono',
        'concepto',
        'monto',
        'mano_de_obra',
        'motivo_no_realizado',
        'tipo_registro',
        'user_id',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function empleado()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}