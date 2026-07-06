<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ModulePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'seguridad-empleados' => ['name' => 'Empleados', 'actions' => ['ver', 'crear', 'editar', 'eliminar']],
            'seguridad-permisos-roles' => ['name' => 'Permisos y Roles', 'actions' => ['ver', 'crear', 'editar', 'eliminar']],
            'seguridad-seguridad' => ['name' => 'Seguridad', 'actions' => ['ver']],
            'core-modulos' => ['name' => 'Modulos', 'actions' => ['ver', 'crear']],
        ];

        foreach ($modules as $slug => $data) {
            $module = Module::firstOrCreate(
                ['slug' => $slug],
                ['name' => $data['name']]
            );

            foreach ($data['actions'] as $action) {
                Permission::firstOrCreate(
                    ['name' => "{$slug}.{$action}", 'guard_name' => 'web'],
                    ['module_id' => $module->id]
                );
            }
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        $admin = User::where('username', 'admin')->first();

        if ($admin) {
            $admin->assignRole($superAdmin);
        }
    }
}
