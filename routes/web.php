<?php

use App\Http\Controllers\CobrosController;
use App\Models\User;
use App\Models\Cobros;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

// metodo para identificar si es empleado o admin en el login
Route::post('/login-process', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->rol === 'admin') {
            return redirect()->route('admin.menu');
        }

        if ($user->rol === 'empleado') {
            return redirect()->route('cobros.cobro');
        }

        return redirect()->route('admin.menu');
    }

    return back()->withErrors([
        'email' => 'Los datos ingresado son incorrectos.',
    ])->onlyInput('email');
})->name('login.post');

// Ruta para cerrar sesión
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

// Rutas de empleado y admin
Route::get('/menu/admin', [CobrosController::class, 'adminmenu'])->name('admin.menu');


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

