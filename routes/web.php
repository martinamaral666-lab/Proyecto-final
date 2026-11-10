<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\CuentaController;
use App\Http\Controllers\CobrosController;
use App\Http\Controllers\InfoClienteController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\InventarioController;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Cobros;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Models\Inventario;

/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

Route::get('/', function (Request $request) {
    $ipKey = 'login-lockout-ip:'.$request->ip();
    $loginWaitSeconds = RateLimiter::tooManyAttempts($ipKey, 1)
        ? RateLimiter::availableIn($ipKey)
        : 0;

    return Inertia::render('admin/auth/login', [
        'loginWaitSeconds' => $loginWaitSeconds,
        'routes' => [
            'loginPost' => route('login.post'),
        ]
    ]);
})->name('login');

Route::post('/login-process', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    $identifier = hash('sha256', strtolower(trim($credentials['email'])).'|'.$request->ip());
    $attemptsKey = 'login-attempts:'.$identifier;
    $lockoutKey = 'login-lockout:'.$identifier;
    $ipKey = 'login-lockout-ip:'.$request->ip();

    if (RateLimiter::tooManyAttempts($lockoutKey, 1) || RateLimiter::tooManyAttempts($ipKey, 1)) {
        $waitSeconds = max(
            RateLimiter::availableIn($lockoutKey),
            RateLimiter::availableIn($ipKey)
        );

        return back()
            ->withErrors([
                'email' => "Demasiados intentos. Espera {$waitSeconds} segundos antes de volver a intentar.",
            ])
            ->with('login_wait_seconds', $waitSeconds)
            ->onlyInput('email');
    }

    if (Auth::attempt($credentials)) {
        RateLimiter::clear($attemptsKey);
        RateLimiter::clear($lockoutKey);
        RateLimiter::clear($ipKey);
        $request->session()->regenerate();
        $user = Auth::user();

        $role = $user->role ?? $user->rol ?? '';

        if ($role === 'admin') {
            return redirect()->route('admin.menu');
        }

        if ($role === 'empleado') {
            return redirect()->route('cobros.empleado');
        }

        Auth::logout();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'El usuario no tiene un rol válido.',
            ]);
    }

    RateLimiter::hit($attemptsKey, 1800);

    if (RateLimiter::tooManyAttempts($attemptsKey, 5)) {
        RateLimiter::hit($lockoutKey, 30);
        RateLimiter::hit($ipKey, 30);

        return back()
            ->withErrors([
                'email' => 'Alcanzaste 5 intentos incorrectos. Espera 30 segundos antes de volver a intentar.',
            ])
            ->with('login_wait_seconds', 30)
            ->onlyInput('email');
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

/*
|--------------------------------------------------------------------------
| RECUPERACIÓN DE CONTRASEÑA
|--------------------------------------------------------------------------
*/


Route::get('/forgot-password', function (Request $request) {
    $cooldownUntil = (int) $request->session()->get('password_reset_cooldown_until', 0);
    $resetWaitSeconds = max(0, $cooldownUntil - now()->timestamp);

    return Inertia::render('admin/auth/forgot-password', compact('resetWaitSeconds'));
})->middleware(['auth', 'admin'])->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    $request->validate([
        'email' => ['required', 'email'],
    ], [
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'El correo electrónico no es válido.',
    ]);

    $cooldownUntil = (int) $request->session()->get('password_reset_cooldown_until', 0);
    $waitSeconds = max(0, $cooldownUntil - now()->timestamp);

    if ($waitSeconds > 0) {
        return back()
            ->withErrors([
                'email' => "Espera {$waitSeconds} segundos antes de solicitar otro enlace.",
            ])
            ->with('reset_wait_seconds', $waitSeconds)
            ->onlyInput('email');
    }

    $request->session()->put('password_reset_cooldown_until', now()->addSeconds(30)->timestamp);

    try {
        $status = Password::sendResetLink($request->only('email'));
    } catch (\Throwable $e) {
        Log::error('Error al enviar el correo de recuperación: ' . $e->getMessage());

        return back()
            ->withErrors([
                'email' => 'No pudimos enviar el correo. Revisá la configuración de correo del servidor.',
            ])
            ->withInput();
    }

    if ($status === Password::RESET_LINK_SENT) {
        return back()->with(
            'status',
            'Te enviamos un enlace para restablecer tu contraseña.'
        );
    }

    $mensaje = match ($status) {
        Password::INVALID_USER => 'No encontramos una cuenta con ese correo electrónico.',
        Password::RESET_THROTTLED => 'Ya se envió un enlace hace poco. Esperá un momento antes de pedir otro.',
        default => 'No se pudo enviar el enlace. Intentá de nuevo.',
    };

    return back()
        ->withErrors(['email' => $mensaje])
        ->with('reset_wait_seconds', 30)
        ->withInput();
})->middleware(['auth', 'admin'])->name('password.email');

Route::get('/reset-password/{token}', function (string $token, Request $request) {
    return Inertia::render('admin/auth/reset-password', [
        'token' => $token,
        'email' => (string) $request->query('email', ''),
    ]);
})->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => ['required'],
        'email' => ['required', 'email'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ], [
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'El correo electrónico no es válido.',
        'password.required' => 'La nueva contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
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
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Tu contraseña fue restablecida correctamente. Ya podés iniciar sesión.'
            );
    }

    return back()
        ->withErrors([
            'email' => 'El enlace para restablecer la contraseña no es válido o ya venció.',
        ])
        ->withInput();
})->name('password.update');

/*
|--------------------------------------------------------------------------
| MENÚ Y CUENTA DEL ADMINISTRADOR
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->get('/menu/admin', [InventarioController::class, 'index'])
    ->name('admin.menu');

Route::middleware(['auth', 'admin'])
    ->get('/menu/admin/inventario', [InventarioController::class, 'index'])
    ->name('inventario.index');

Route::middleware(['auth', 'admin'])
    ->get('/admin/cuenta', [CuentaController::class, 'edit'])
    ->name('admin.cuenta');

Route::middleware(['auth', 'admin'])
    ->put('/admin/cuenta', [CuentaController::class, 'update'])
    ->name('admin.cuenta.update');

/*
|--------------------------------------------------------------------------
| COBROS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'empleado'])
    ->get('/crear/cobros', function (Request $request) {
        $cobros = Cobros::paginate(10);
        $itemsInventario = Inventario::query()
            ->select('id', 'nombre_item', 'precio', 'stock_actual')
            ->orderBy('nombre_item')
            ->get();
        $cooldownKey = 'charge-receipt:'.$request->user()->getAuthIdentifier();
        $receiptWaitSeconds = RateLimiter::tooManyAttempts($cooldownKey, 1)
            ? RateLimiter::availableIn($cooldownKey)
            : 0;

        return Inertia::render('admin/cobros/cobro', compact('cobros', 'itemsInventario', 'receiptWaitSeconds'));
    })->name('cobros.empleado');

Route::middleware(['auth', 'admin'])
    ->get('/admin/crear/cobros', function (Request $request) {
        $cobros = Cobros::paginate(10);
        $itemsInventario = Inventario::query()
            ->select('id', 'nombre_item', 'precio', 'stock_actual')
            ->orderBy('nombre_item')
            ->get();
        $cooldownKey = 'charge-receipt:'.$request->user()->getAuthIdentifier();
        $receiptWaitSeconds = RateLimiter::tooManyAttempts($cooldownKey, 1)
            ? RateLimiter::availableIn($cooldownKey)
            : 0;

        return Inertia::render('admin/cobros/cobro', compact('cobros', 'itemsInventario', 'receiptWaitSeconds'));
    })->name('cobros.cobros');

Route::middleware(['auth', 'admin'])
    ->get('/cobros', function (Request $request) {
        $cobros = Cobros::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('nombre_cliente', 'like', "%{$search}%")
                      ->orWhere('telefono', 'like', "%{$search}%")
                      ->orWhere('concepto', 'like', "%{$search}%");
                });
            })
            ->latest()
            // withQueryString() hace que al pasar de página se mantenga la búsqueda
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('admin/cobros/index', [
            'cobros' => $cobros,
            'filters' => $request->only('search'),
        ]);
    })->name('cobros.index');

Route::middleware(['auth', 'admin'])
    ->get('/cobros/{cobro}/pdf', [CobrosController::class, 'pdf'])
    ->name('cobros.pdf');
/*
|--------------------------------------------------------------------------
| EMPLEADOS (ADMIN)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->get('/admin/empleados/create', function (Request $request) {
        $cooldownKey = 'employee-create:'.$request->user()->getAuthIdentifier();
        $employeeWaitSeconds = RateLimiter::tooManyAttempts($cooldownKey, 1)
            ? RateLimiter::availableIn($cooldownKey)
            : 0;

        return Inertia::render('admin/empleados/create', compact('employeeWaitSeconds'));
    })->name('admin.empleados.create');

Route::post('/admin/empleados/store', function (Request $request) {
    $cooldownKey = 'employee-create:'.$request->user()->getAuthIdentifier();

    if (RateLimiter::tooManyAttempts($cooldownKey, 1)) {
        $waitSeconds = RateLimiter::availableIn($cooldownKey);

        return back()
            ->withErrors([
                'employee' => "Espera {$waitSeconds} segundos antes de crear otro empleado.",
            ])
            ->with('employee_wait_seconds', $waitSeconds);
    }

    RateLimiter::hit($cooldownKey, 10);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6',
    ]);

    User::create([
        'name' => $validated['name'],
        'email' => trim($validated['email']),
        'password' => Hash::make($validated['password']),
        'rol' => 'empleado',
    ]);

    return redirect()->route('admin.menu');
})->middleware(['auth', 'admin'])->name('admin.empleados.store');

/*
|--------------------------------------------------------------------------
| RUTAS DEL INVENTARIO
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {

    // Vistas y formularios (apuntando al controlador)
    Route::get('/inventario/crear', [InventarioController::class, 'create'])->name('inventario.crear');
    Route::get('/inventario/{id}/editar', [InventarioController::class, 'edit'])->name('inventario.editar');

    // Gestión de datos del inventario
    Route::get('/menu/admin/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::post('/menu/admin/inventario', [InventarioController::class, 'store'])->name('inventario.store');

    // Acciones sobre ítems específicos
    Route::patch('/inventario/{id}/uso', [InventarioController::class, 'uso'])->name('inventario.uso');
    Route::delete('/inventario/{id}', [InventarioController::class, 'destroy'])->name('inventario.destroy');

    Route::put('/inventario/{id}', [InventarioController::class, 'update'])->name('inventario.update');
});

/*
|--------------------------------------------------------------------------
| GUARDAR COBRO
|--------------------------------------------------------------------------
*/

Route::post('/cobros', [CobrosController::class, 'store'])
    ->middleware('auth')
    ->name('cobros.store');


/*
|--------------------------------------------------------------------------
| informacion de clientes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/infodeclientes', [InfoClienteController::class, 'index'])->name('infodeclientes.index');
    Route::get('/infodeclientes/{id}', [InfoClienteController::class, 'show'])->name('infodeclientes.show');
    Route::get('/infodeclientes/{id}/edit', [InfoClienteController::class, 'edit'])->name('infodeclientes.edit');
    Route::put('/infodeclientes/{id}', [InfoClienteController::class, 'update'])->name('infodeclientes.update');
});


/*
|--------------------------------------------------------------------------
| listado de empleados
|--------------------------------------------------------------------------
*/


Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Rutas de Empleados protegidas
    Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
    Route::get('/empleados/create', [EmpleadoController::class, 'create'])->name('empleados.create');
    Route::post('/empleados', [EmpleadoController::class, 'store'])->name('empleados.store');
    Route::get('/empleados/{id}/edit', [EmpleadoController::class, 'edit'])->name('empleados.edit');
    Route::put('/empleados/{id}', [EmpleadoController::class, 'update'])->name('empleados.update');
    Route::delete('/empleados/{id}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');
});

Route::post('/cobro/store', [CobrosController::class, 'store'])
    ->middleware('auth')
    ->name('cobros.store');

   Route::get('/menu/modulos', function () {
    return Inertia::render('admin/Modulos');
})->middleware(['auth'])->name('admin.modulos');
