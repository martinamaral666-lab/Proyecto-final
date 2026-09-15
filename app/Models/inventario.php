<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventario extends Model {
    use HasFactory;

    protected $fillable = [
        'nombre_item',
        'categoria',
        'stock_actual',
        'unidad',
        'ubicacion',
        'estado'
    ];
}
