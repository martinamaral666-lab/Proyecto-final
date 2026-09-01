<?php

use App\Http\Controllers\CobrosController;
use App\Models\User;
use App\Models\Cobros;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

<<<<<<< HEAD
// Grupo de rutas protegidas por el middleware de autenticación ('auth')
Route::middleware(['auth'])->group(function () {
    // Ruta POST para guardar/procesar un nuevo cobro en la base de datos
    Route::post('/cobros', [CobrosController::class, 'store'])->name('cobros.store');

});


// Ruta GET para la vista del menú de administración con paneles estadísticos
Route::get('/admin/menu', [CobrosController::class, 'adminmenu'])->name('admin.menu');

// Ruta GET para mostrar el formulario de creación de un nuevo cobro
Route::get('/cobros/crear', [CobrosController::class, 'create'])->name('cobros.create');

// Ruta GET para listar los cobros del usuario autenticado
Route::get('/cobros', [CobrosController::class, 'index'])->name('cobros.index');

// Ruta GET para acceder de forma pública al recibo mediante su token de seguridad
Route::get('/recibo/{token}', [CobrosController::class, 'showPublicReceipt'])->name('recibo.publico');

// Ruta principal renderizada mediante Inertia.js (Página de bienvenida)
Route::inertia('/', 'Welcome')->name('home');

// Ruta GET de pruebas para simular el registro de un cobro desde un formulario HTML embebido
Route::get('/probar-cobro', function () {

    // Autentica automáticamente al usuario con ID 1 en la sesión actual
    auth()->loginUsingId(1);

    // Obtiene el listado completo de clientes de la base de datos
    $clientes = Cliente::all();

    // Mapea la lista de clientes para generar las opciones HTML del select
    $options = $clientes->map(fn($c) => "<option value='{$c->id}'>{$c->nombre} ({$c->telefono})</option>")->join('');

    // Retorna una respuesta HTML directa con el formulario de prueba
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


    // Definición rápida de una ruta de login de prueba que fuerza el inicio de sesión con ID 1
    Route::get('/login', function (){
        auth()->loginUsingId(1);
        return redirect()->route('cobros.index');
    })->name('login');
});
=======
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

>>>>>>> correcion-de-errores-de-rutas1
