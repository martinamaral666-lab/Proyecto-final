<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = User::where('rol', 'empleado')->get();

        return view('admin.empleados.index', compact('empleados'));
    }

    public function create()
    {
        return view('admin.empleados.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'rol' => 'empleado' // Aseguramos que se guarde con el rol correspondiente
        ]);

        return redirect()->route('admin.empleados.index')->with('success', 'Empleado registrado con éxito.');
    }

    public function edit($id)
    {
        $empleado = User::findOrFail($id);
        return view('admin.empleados.editar', compact('empleado'));
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
