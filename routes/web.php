<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CobrosController;
use App\Models\Cliente;

Route::middleware(['auth'])->group(function () {
    Route::post('/cobros', [CobrosController::class, 'store'])->name('cobros.store');
});
Route::get('/admin/menu', [CobrosController::class, 'adminmenu'])->name('admin.menu');

Route::get('/cobros/crear', [CobrosController::class, 'create'])->name('cobros.create');

Route::get('/cobros', [CobrosController::class, 'index'])->name('cobros.index');

Route::get('/recibo/{token}', [CobrosController::class, 'showPublicReceipt'])->name('recibo.publico');

Route::inertia('/', 'Welcome')->name('home');



Route::get('/probar-cobro', function () {

    auth()->loginUsingId(1);

    $clientes = Cliente::all();

    $options = $clientes->map(fn($c) => "<option value='{$c->id}'>{$c->nombre} ({$c->telefono})</option>")->join('');

    return '
    <form action="'.route('cobros.store').'" method="POST" style="font-family:sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px;">
        '.csrf_field().'
        <h2>Registrar Cobro (Modo Prueba)</h2>

        <label>Cliente:</label><br>
        <select name="cliente_id" style="width:100%; margin-bottom:15px; padding:8px;">
            '.$options.'
        </select><br>

        <label>Monto ($):</label><br>
        <input type="number" name="cantidad" value="1500" style="width:100%; margin-bottom:15px; padding:8px;"><br>

        <label>Concepto:</label><br>
        <input type="text" name="concepto" value="Cuota de servicio" style="width:100%; margin-bottom:15px; padding:8px;"><br>

        <button type="submit" style="background:green; color:white; border:none; padding:10px 15px; cursor:pointer; width:100%;">
            Cobrar y enviar por WhatsApp
        </button>
    </form>
    ';

    Route::get('/login', function (){
        auth()->loginUsingId(1);
        return redirect()->route('cobros.index');
    })->name('login');
});
