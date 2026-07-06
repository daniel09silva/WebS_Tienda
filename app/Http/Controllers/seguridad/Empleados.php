<?php

namespace App\Http\Controllers\seguridad;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class Empleados extends Controller
{
  public function index()
  {
    $empleados = User::orderBy('name')->get();

    return view('content.seguridad.empleados', compact('empleados'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'username' => ['required', 'string', 'max:255', 'unique:users,username'],
      'email' => ['required', 'email', 'max:255', 'unique:users,email'],
      'password' => ['required', 'string', 'min:6', 'confirmed'],
    ]);

    User::create($request->only('name', 'username', 'email', 'password'));

    return redirect()->route('seguridad-empleados')->with('success', 'Empleado creado correctamente.');
  }

  public function update(Request $request, User $empleado)
  {
    $request->validate([
      'edit_name' => ['required', 'string', 'max:255'],
      'edit_username' => ['required', 'string', 'max:255', 'unique:users,username,' . $empleado->id],
      'edit_email' => ['required', 'email', 'max:255', 'unique:users,email,' . $empleado->id],
    ]);

    $empleado->update([
      'name' => $request->input('edit_name'),
      'username' => $request->input('edit_username'),
      'email' => $request->input('edit_email'),
    ]);

    return redirect()->route('seguridad-empleados')->with('success', 'Empleado actualizado correctamente.');
  }

  public function destroy(Request $request, User $empleado)
  {
    $request->validate([
      'confirm_password' => ['required', 'current_password'],
    ]);

    $empleado->delete();

    return redirect()->route('seguridad-empleados')->with('success', 'Empleado eliminado correctamente.');
  }
}
