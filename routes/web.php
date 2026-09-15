<?php

use App\Http\Controllers\CobrosController;
use App\Http\Controllers\Admin\CuentaController;
use App\Models\User;
use App\Models\Cobros;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login');
})->name('login');


Route::post('/login-process', function (Request $request) {

    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {

        $request->session()->regenerate();

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | ADMINISTRADOR
        |--------------------------------------------------------------------------
        */

        if ($user->rol === 'admin') {
            return redirect()->route('admin.menu');
        }

        /*
        |--------------------------------------------------------------------------
        | EMPLEADO
        |--------------------------------------------------------------------------
        */

        if ($user->rol === 'empleado') {
            return redirect()->route('cobros.empleado');
        }

        /*
        |--------------------------------------------------------------------------
        | ROL NO RECONOCIDO
        |--------------------------------------------------------------------------
        */

        Auth::logout();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'El usuario no tiene un rol válido.',
            ]);
    }

    return back()
        ->withErrors([
            'email' => 'Los datos ingresados son incorrectos.',
        ])
        ->onlyInput('email');

})->name('login.post');


/*
|--------------------------------------------------------------------------
| CERRAR SESIÓN
|--------------------------------------------------------------------------
*/

Route::post('/logout', function (Request $request) {

    Auth::logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect()->route('login');

})->name('logout');

// Rutas de empleado y admin
Route::middleware(['auth', 'admin'])->get('/menu/admin', [CobrosController::class, 'adminmenu'])->name('admin.menu');

// Ruta de Cobros
Route::middleware(['auth', 'empleado'])->get('/crear/cobros', function () {
    $cobros = Cobros::paginate(10);

    return view('cobros.cobro', compact('cobros'));
})->name('cobros.cobro');

Route::middleware(['auth', 'admin'])->get('/cobros', function () {
    $cobros = Cobros::paginate(10);

    return view('cobros.index', compact('cobros'));
})->name('cobros.index');

Route::middleware(['auth', 'admin'])->get('/cobros/{cobro}/pdf', [CobrosController::class, 'pdf'])
    ->name('cobros.pdf');

Route::middleware(['auth', 'admin'])->get('/admin/empleados/crear', function () {
    return view('admin.empleados.create');
})->name('admin.empleados.create');

/*
|--------------------------------------------------------------------------
| RECUPERACIÓN DE CONTRASEÑA
|--------------------------------------------------------------------------
*/

Route::get('/forgot-password', function () {

    return view('auth.forgot-password');

})->name('password.request');


Route::post('/forgot-password', function (Request $request) {

    $request->validate([
        'email' => [
            'required',
            'email'
        ],
    ], [
        'email.required' =>
            'El correo electrónico es obligatorio.',

        'email.email' =>
            'El correo electrónico no es válido.',
    ]);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    if ($status === Password::RESET_LINK_SENT) {

        return back()->with(
            'status',
            'Te enviamos un enlace para restablecer tu contraseña.'
        );
    }

    return back()
        ->withErrors([
            'email' =>
                'No encontramos una cuenta con ese correo electrónico.'
        ])
        ->withInput();

})->name('password.email');


Route::get('/reset-password/{token}', function (
    string $token,
    Request $request
) {

    return view('auth.reset-password', [
        'token' => $token,
        'email' => $request->email,
    ]);

})->name('password.reset');


Route::post('/reset-password', function (Request $request) {

    $request->validate([
        'token' => [
            'required'
        ],

        'email' => [
            'required',
            'email'
        ],

        'password' => [
            'required',
            'string',
            'min:8',
            'confirmed'
        ],
    ], [
        'email.required' =>
            'El correo electrónico es obligatorio.',

        'email.email' =>
            'El correo electrónico no es válido.',

        'password.required' =>
            'La nueva contraseña es obligatoria.',

        'password.min' =>
            'La contraseña debe tener al menos 8 caracteres.',

        'password.confirmed' =>
            'Las contraseñas no coinciden.',
    ]);

    $status = Password::reset(
        $request->only(
            'email',
            'password',
            'password_confirmation',
            'token'
        ),

        function (User $user, string $password) {

            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();
        }
    );

    if ($status === Password::PASSWORD_RESET) {

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Tu contraseña fue restablecida correctamente. Ya podés iniciar sesión.'
            );
    }

    return back()
        ->withErrors([
            'email' =>
                'El enlace para restablecer la contraseña no es válido o ya venció.'
        ])
        ->withInput();

})->name('password.update');


/*
|--------------------------------------------------------------------------
| MENÚ ADMINISTRADOR
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->get(
        '/menu/admin',
        [CobrosController::class, 'adminmenu']
    )
    ->name('admin.menu');


/*
|--------------------------------------------------------------------------
| CUENTA DEL ADMINISTRADOR
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->get(
        '/admin/cuenta',
        [CuentaController::class, 'edit']
    )
    ->name('admin.cuenta');


Route::middleware(['auth', 'admin'])
    ->put(
        '/admin/cuenta',
        [CuentaController::class, 'update']
    )
    ->name('admin.cuenta.update');


/*
|--------------------------------------------------------------------------
| COBROS - EMPLEADO
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'empleado'])
    ->get('/crear/cobros', function () {

        $cobros = Cobros::paginate(10);

        return view(
            'cobros.cobro',
            compact('cobros')
        );

    })
    ->name('cobros.empleado');


/*
|--------------------------------------------------------------------------
| COBROS - ADMINISTRADOR
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->get('/admin/crear/cobros', function () {

        $cobros = Cobros::paginate(10);

        return view(
            'cobros.cobro',
            compact('cobros')
        );

    })
    ->name('cobros.cobros');


/*
|--------------------------------------------------------------------------
| LISTADO DE COBROS - ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->get('/cobros', function () {

        $cobros = Cobros::paginate(10);

        return view(
            'cobros.index',
            compact('cobros')
        );

    })
    ->name('cobros.index');


/*
|--------------------------------------------------------------------------
| REGISTRAR EMPLEADO
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->get('/admin/empleados/crear', function () {

        return view('admin.empleados.create');

    })
    ->name('admin.empleados.create');


Route::post('/admin/store', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6',
    ]);

    User::create([
        'name' => $request->name,
        'email' => trim($request->email),
        'password' => Hash::make($request->password),
        'rol' => 'empleado',
    ]);

//Rutas del inventario

Route::get('/inventario/crear', function () {
    return view('admin.inventario');
})->name('inventario.crear');

Route::get('/inventario/editar', function () {
    Return view('admin.inventario');
})->name('inventario.editar');

Route::patch('/menu/admin/inventario/{id}/uso', [InventarioController::class, 'uso'])->name('inventario.uso');

Route::get('/menu/admin/inventario', [InventarioController::class, 'index'])->name('inventario.index');

Route::post('/menu/admin/inventario', [InventarioController::class, 'store'])->name('inventario.store');

Route::delete('/menu/admin/inventario/{id}', [InventarioController::class, 'destroy'])->name('inventario.destroy');

    $request->validate([
        'name' =>
            'required|string|max:255',

        'email' =>
            'required|string|email|max:255|unique:users',

        'password' =>
            'required|string|min:6',
    ]);

    User::create([
        'name' =>
            $request->name,

        'email' =>
            trim($request->email),

        'password' =>
            Hash::make($request->password),

        'rol' =>
            'empleado',
    ]);

    return redirect()->route('admin.menu');

})->name('admin.empleados.store');


/*
|--------------------------------------------------------------------------
| GUARDAR COBRO
|--------------------------------------------------------------------------
*/

Route::post(
    '/cobro/store',
    [CobrosController::class, 'store']
)->name('cobros.store');
