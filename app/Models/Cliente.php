<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    /** @use HasFactory<\Database\Factories\ClienteFactory> */
    use HasFactory;

    protected $fillable = ['nombre', 'telefono', 'direccion'];
    public function cobros()
    {
        return $this->hasMany(cobros::class);
    }
}
