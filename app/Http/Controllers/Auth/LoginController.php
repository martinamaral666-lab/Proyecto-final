<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $cooldownKey = 'login-attempt:'.$request->ip();
        $loginWaitSeconds = RateLimiter::tooManyAttempts($cooldownKey, 5)
            ? RateLimiter::availableIn($cooldownKey)
            : 0;

        return Inertia::render('admin/auth/login', [
            'loginWaitSeconds' => max(0, $loginWaitSeconds),
            'routes' => [
                'loginPost' => route('login.post'),
            ]
        ]);
    }

    public function login(Request $request)
    {
        $cooldownKey = 'login-attempt:'.$request->ip();

        if (RateLimiter::tooManyAttempts($cooldownKey, 5)) {
            $waitSeconds = RateLimiter::availableIn($cooldownKey);
            return back()
                ->withErrors([
                    'email' => "Demasiados intentos de inicio de sesión. Espera {$waitSeconds} segundos.",
                ])
                ->with('login_wait_seconds', $waitSeconds);
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            RateLimiter::clear($cooldownKey);
            $request->session()->regenerate();

            $user = Auth::user();
            // Nota: verifica si en tu base de datos el campo se llama 'role' o 'rol' según los modelos anteriores
            $role = $user->role ?? $user->rol ?? '';

            if ($role === 'admin') {
                return redirect()->route('admin.menu');
            }

            return redirect()->route('empleado.cobro');
        }

        RateLimiter::hit($cooldownKey, 60);
        $waitSeconds = RateLimiter::availableIn($cooldownKey);

        $tooMany = RateLimiter::tooManyAttempts($cooldownKey, 5);

        return back()
            ->withErrors([
                'email' => 'Las credenciales proporcionadas no son correctas.',
            ])
            ->with('login_wait_seconds', $tooMany ? $waitSeconds : 0)
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
