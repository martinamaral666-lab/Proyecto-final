<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class InfoCliente extends Model
{
    use HasFactory;

    protected $table = 'infodeclientes';
    protected $fillable = ['name', 'phone', 'email'];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'infodecliente_id');
    }

    public function currentMonthSalesCount()
    {
        return $this->sales()
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count();
    }

    public function isRegularCustomer()
    {
        return $this->currentMonthSalesCount() >= 3;
    }
}
