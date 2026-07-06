<?php

namespace App\Http\Controllers\core;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Modulos extends Controller
{
  public function index()
  {
    $modulos = Module::with('permissions')->orderBy('name')->get();

    return view('content.core.modulos', compact('modulos'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'description' => ['nullable', 'string', 'max:255'],
    ]);

    $slug = Str::slug($request->input('name'));

    if (Module::where('slug', $slug)->exists()) {
      return back()->withErrors(['name' => 'Ya existe un módulo con ese nombre.'])->withInput();
    }

    Module::create([
      'name' => $request->input('name'),
      'slug' => $slug,
      'description' => $request->input('description'),
    ]);

    return redirect()->route('core-modulos')->with('success', 'Módulo creado correctamente.');
  }

  public function storePermission(Request $request, Module $module)
  {
    $request->validate([
      'action' => ['required', 'string', 'max:255', 'alpha_dash'],
    ]);

    $name = $module->slug . '.' . Str::slug($request->input('action'));

    if (Permission::where('name', $name)->exists()) {
      return back()->withErrors(['action' => 'Ese módulo ya tiene un permiso con esa acción.'])->withInput();
    }

    Permission::create([
      'name' => $name,
      'guard_name' => 'web',
      'module_id' => $module->id,
    ]);

    return redirect()->route('core-modulos')->with('success', 'Permiso agregado correctamente.');
  }
}
