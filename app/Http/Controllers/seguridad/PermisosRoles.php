<?php

namespace App\Http\Controllers\seguridad;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class PermisosRoles extends Controller
{
  public function index()
  {
    $roles = Role::with('permissions')->orderBy('name')->get();
    $modulos = Module::with('permissions')->orderBy('name')->get();

    return view('content.seguridad.permisos-roles', compact('roles', 'modulos'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
      'permissions' => ['array'],
      'permissions.*' => ['integer', 'exists:permissions,id'],
    ]);

    $role = Role::create([
      'name' => $request->input('name'),
      'guard_name' => 'web',
    ]);

    $role->syncPermissions(array_map('intval', $request->input('permissions', [])));

    return redirect()->route('seguridad-permisos-roles')->with('success', 'Rol creado correctamente.');
  }

  public function update(Request $request, Role $role)
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
      'permissions' => ['array'],
      'permissions.*' => ['integer', 'exists:permissions,id'],
    ]);

    $role->update(['name' => $request->input('name')]);
    $role->syncPermissions(array_map('intval', $request->input('permissions', [])));

    return redirect()->route('seguridad-permisos-roles')->with('success', 'Rol actualizado correctamente.');
  }

  public function destroy(Role $role)
  {
    if ($role->name === 'Super Admin') {
      return back()->withErrors(['name' => 'El rol Super Admin no se puede eliminar.']);
    }

    $role->delete();

    return redirect()->route('seguridad-permisos-roles')->with('success', 'Rol eliminado correctamente.');
  }
}
