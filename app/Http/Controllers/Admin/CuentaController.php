<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CuentaController extends Controller
{
    public function edit()
    {
        $usuario = Auth::user();

        return view('admin.cuenta', compact('usuario'));
    }


    public function update(Request $request)
    {
        $usuario = Auth::user();


        /*
        |--------------------------------------------------------------------------
        | VALIDAR DATOS GENERALES
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $usuario->id
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20'
            ],

        ], [

            'email.required' =>
                'El correo electrónico es obligatorio.',

            'email.email' =>
                'El correo electrónico no es válido.',

            'email.unique' =>
                'Ese correo electrónico ya está siendo utilizado.',

            'telefono.max' =>
                'El teléfono no puede superar los 20 caracteres.',
        ]);


        /*
        |--------------------------------------------------------------------------
        | COMPROBAR SI SE QUIERE CAMBIAR LA CONTRASEÑA
        |--------------------------------------------------------------------------
        */

        $quiereCambiarPassword =
            $request->filled('password_actual') ||
            $request->filled('password') ||
            $request->filled('password_confirmation');


        if ($quiereCambiarPassword) {


            /*
            |--------------------------------------------------------------------------
            | CONTRASEÑA ACTUAL
            |--------------------------------------------------------------------------
            */

            if (!$request->filled('password_actual')) {

                return back()
                    ->withErrors([
                        'password_actual' =>
                            'Debés ingresar tu contraseña actual.'
                    ])
                    ->withInput();
            }


            /*
            |--------------------------------------------------------------------------
            | COMPROBAR CONTRASEÑA ACTUAL
            |--------------------------------------------------------------------------
            */

            if (!Hash::check(
                $request->password_actual,
                $usuario->password
            )) {

                return back()
                    ->withErrors([
                        'password_actual' =>
                            'La contraseña actual es incorrecta.'
                    ])
                    ->withInput();
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDAR NUEVA CONTRASEÑA
            |--------------------------------------------------------------------------
            */

            $request->validate([

                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed'
                ],

            ], [

                'password.required' =>
                    'Debés ingresar una nueva contraseña.',

                'password.min' =>
                    'La nueva contraseña debe tener al menos 8 caracteres.',

                'password.confirmed' =>
                    'La nueva contraseña y su confirmación no coinciden.',
            ]);


            /*
            |--------------------------------------------------------------------------
            | COMPROBAR QUE SEA DIFERENTE
            |--------------------------------------------------------------------------
            */

            if (Hash::check(
                $request->password,
                $usuario->password
            )) {

                return back()
                    ->withErrors([
                        'password' =>
                            'La nueva contraseña debe ser diferente de la contraseña actual.'
                    ])
                    ->withInput();
            }


            /*
            |--------------------------------------------------------------------------
            | GUARDAR NUEVA CONTRASEÑA
            |--------------------------------------------------------------------------
            */

            $usuario->password = Hash::make(
                $request->password
            );
        }


        /*
        |--------------------------------------------------------------------------
        | GUARDAR DATOS DE LA CUENTA
        |--------------------------------------------------------------------------
        */

        $usuario->email =
            $request->email;

        $usuario->telefono =
            $request->telefono;

        $usuario->save();


        /*
        |--------------------------------------------------------------------------
        | MENSAJE DE ÉXITO
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('admin.cuenta')
            ->with(
                'success',
                'Los datos de la cuenta se actualizaron correctamente.'
            );
    }
}