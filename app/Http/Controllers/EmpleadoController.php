<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Inertia;

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = User::where('rol', 'empleado')->get();

        return Inertia::render('admin/empleados/index', [
            'empleados' => $empleados,
            'routes' => [
                'adminMenu' => route('admin.menu'),
                'empleadosCreate' => route('admin.empleados.create'),
            ]
        ]);
    }

    public function create()
    {
        $cooldownKey = 'employee-create:'.request()->user()->getAuthIdentifier();
        $employeeWaitSeconds = RateLimiter::availableIn($cooldownKey);

        return Inertia::render('admin/empleados/create', [
            'employeeWaitSeconds' => max(0, $employeeWaitSeconds),
            'routes' => [
                'adminMenu' => route('admin.menu'),
                'empleadosStore' => route('admin.empleados.store'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $cooldownKey = 'employee-create:'.$request->user()->getAuthIdentifier();

        if (RateLimiter::tooManyAttempts($cooldownKey, 1)) {
            $waitSeconds = RateLimiter::availableIn($cooldownKey);

            return back()
                ->withErrors([
                    'save' => "Espera {$waitSeconds} segundos antes de registrar otro empleado.",
                ])
                ->with('employee_wait_seconds', $waitSeconds);
        }

        RateLimiter::hit($cooldownKey, 15);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'rol' => 'empleado'
        ]);

        return redirect()->route('admin.empleados.index')->with('success', 'Empleado registrado con éxito.');
    }

    public function edit($id)
    {
        $empleado = User::findOrFail($id);

        return Inertia::render('admin/empleados/editar', [
            'empleado' => $empleado,
            'routes' => [
                'adminMenu' => route('admin.menu'),
                'empleadosIndex' => route('admin.empleados.index'),
                'empleadosUpdate' => route('admin.empleados.update', $empleado->id),
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $empleado = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $empleado->id,
            'password' => 'nullable|min:6',
        ]);

        $empleado->name = $request->name;
        $empleado->email = $request->email;

        if ($request->filled('password')) {
            $empleado->password = bcrypt($request->password);
        }

        $empleado->save();

        return redirect()->route('admin.empleados.index')->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy($id)
    {
        $empleado = User::findOrFail($id);
        $empleado->delete();

        return redirect()->route('admin.empleados.index')->with('success', 'Empleado dado de baja correctamente.');
    }
}
