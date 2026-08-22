<?php

use App\Http\Controllers\Admin\AdminRegistroEmpleados;
use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CobrosController;
use App\Models\Cliente;
use App\Http\Controllers\Auth\LoginController;

Route::get('/css/{filename}', function ($filename) {
    $path = resource_path('views/css/' . $filename);
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path, ['Content-Type' => 'text/css']);
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// 2. RUTAS PROTEGIDAS (Solo accesibles una vez iniciada la sesión)
Route::middleware(['auth'])->group(function () {

    // Ruta de Empleado
    Route::get('/cobro', [CobrosController::class, 'create'])->name('empleado.cobro');
Route::post('/cobro', [CobrosController::class, 'store'])->name('cobro.store');

    // Rutas de Admin
    Route::get('/admin/empleados/registrar', [AdminRegistroEmpleados::class, 'create'])->name('admin.empleados.create');
Route::post('/admin/empleados/registrar', [AdminRegistroEmpleados::class, 'store'])->name('admin.empleados.store');

    Route::get('/admin/clientes/registrar', [ClienteController::class, 'create'])->name('clientes.create');
    Route::post('/admin/clientes/registrar', [ClienteController::class, 'store'])->name('clientes.store');
});


// Ruta de Cobros
Route::get('/crear/cobros', function () {
    $cobros = Cobros::paginate(10);
    return view('cobros.cobro', compact('cobros'));
})->name('cobros.cobro');

Route::get('/cobros', function () {
    $cobros = Cobros::paginate(10);
    return view('cobros.index', compact('cobros'));
})->name('cobros.index');

Route::get('/admin/empleados/crear', function () {
    return view('admin.empleados.create');
})->name('admin.empleados.create');

Route::post('/cobro/store', [CobrosController::class, 'store'])->name('cobros.store');

// metodo para poder registrar al empleado y que se guarde en la base de datos
Route::post('/admin/store', function (Request $request) {
 $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6',
 ]);

 User::create([
        'name'     => $request->name,
        'email'    => trim($request->email),
        'password' => Hash::make($request->password),
        'rol'      => 'empleado',
 ]);
 return redirect()->route('admin.menu');
})->name('admin.empleados.store');

