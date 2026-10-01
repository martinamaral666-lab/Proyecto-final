<?php

use App\Http\Controllers\Admin\CuentaController;
use App\Http\Controllers\CobrosController;
use App\Http\Controllers\InventarioController;
use App\Models\Cobros;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
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

    $identifier = hash('sha256', strtolower(trim($credentials['email'])).'|'.$request->ip());
    $attemptsKey = 'login-attempts:'.$identifier;
    $lockoutKey = 'login-lockout:'.$identifier;

    if (RateLimiter::tooManyAttempts($lockoutKey, 1)) {
        $waitSeconds = RateLimiter::availableIn($lockoutKey);

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
        $request->session()->regenerate();
        $user = Auth::user();

        if ($user->rol === 'admin') {
            return redirect()->route('admin.menu');
        }

        if ($user->rol === 'empleado') {
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

    if (RateLimiter::attempts($attemptsKey) >= 5) {
        RateLimiter::clear($attemptsKey);
        RateLimiter::hit($lockoutKey, 30);

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

    return view('auth.forgot-password', compact('resetWaitSeconds'));
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

    $status = Password::sendResetLink($request->only('email'));

    if ($status === Password::RESET_LINK_SENT) {
        return back()
            ->with('status', 'Te enviamos un enlace para restablecer tu contraseña.')
            ->with('reset_wait_seconds', 30)
            ->withInput();
    }

    return back()
        ->withErrors([
            'email' => 'No encontramos una cuenta con ese correo electrónico.',
        ])
        ->with('reset_wait_seconds', 30)
        ->withInput();
})->middleware(['auth', 'admin'])->name('password.email');

Route::get('/reset-password/{token}', function (string $token, Request $request) {
    return view('auth.reset-password', [
        'token' => $token,
        'email' => $request->email,
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
        $request->only('email', 'password', 'password_confirmation', 'token'),
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
            ->with('success', 'Tu contraseña fue restablecida correctamente. Ya podés iniciar sesión.');
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
    ->get('/menu/admin', [CobrosController::class, 'adminmenu'])
    ->name('admin.menu');

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
        $cooldownKey = 'charge-receipt:'.$request->user()->getAuthIdentifier();
        $receiptWaitSeconds = RateLimiter::tooManyAttempts($cooldownKey, 1)
            ? RateLimiter::availableIn($cooldownKey)
            : 0;

        return view('cobros.cobro', compact('cobros', 'receiptWaitSeconds'));
    })->name('cobros.empleado');

Route::middleware(['auth', 'admin'])
    ->get('/admin/crear/cobros', function (Request $request) {
        $cobros = Cobros::paginate(10);
        $cooldownKey = 'charge-receipt:'.$request->user()->getAuthIdentifier();
        $receiptWaitSeconds = RateLimiter::tooManyAttempts($cooldownKey, 1)
            ? RateLimiter::availableIn($cooldownKey)
            : 0;

        return view('cobros.cobro', compact('cobros', 'receiptWaitSeconds'));
    })->name('cobros.cobros');

Route::middleware(['auth', 'admin'])
    ->get('/cobros', function () {
        $cobros = Cobros::paginate(10);

        return view('cobros.index', compact('cobros'));
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
    ->get('/admin/empleados/crear', function (Request $request) {
        $cooldownKey = 'employee-create:'.$request->user()->getAuthIdentifier();
        $employeeWaitSeconds = RateLimiter::tooManyAttempts($cooldownKey, 1)
            ? RateLimiter::availableIn($cooldownKey)
            : 0;

        return view('admin.empleados.create', compact('employeeWaitSeconds'));
    })->name('admin.empleados.create');

Route::post('/admin/store', function (Request $request) {
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
    Route::get('/inventario/crear', function (Request $request) {
        $cooldownKey = 'inventory-save:'.$request->user()->getAuthIdentifier();
        $saveWaitSeconds = RateLimiter::tooManyAttempts($cooldownKey, 1)
            ? RateLimiter::availableIn($cooldownKey)
            : 0;

        return view('admin.inventario', compact('saveWaitSeconds'));
    })->name('inventario.crear');

    Route::get('/inventario/editar', function () {
        return view('admin.inventario');
    })->name('inventario.editar');

    Route::patch('/menu/admin/inventario/{id}/uso', [InventarioController::class, 'uso'])->name('inventario.uso');

    Route::get('/menu/admin/inventario', [InventarioController::class, 'index'])->name('inventario.index');

    Route::post('/menu/admin/inventario', [InventarioController::class, 'store'])->name('inventario.store');

    Route::delete('/menu/admin/inventario/{id}', [InventarioController::class, 'destroy'])->name('inventario.destroy');
});

/*
|--------------------------------------------------------------------------
| GUARDAR COBRO
|--------------------------------------------------------------------------
*/

Route::post('/cobro/store', [CobrosController::class, 'store'])
    ->middleware('auth')
    ->name('cobros.store');
