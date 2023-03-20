<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()['cache']->forget('spatie.permission.cache');

        Permission::create(['guard_name' => 'web', 'name' => 'usuarios-editar']);
        Permission::create(['guard_name' => 'web', 'name' => 'usuarios-borrar']);
        Permission::create(['guard_name' => 'web', 'name' => 'usuarios-ver']);
        Permission::create(['guard_name' => 'web', 'name' => 'permisos-ver']);
        Permission::create(['guard_name' => 'web', 'name' => 'roles-ver']);
        Permission::create(['guard_name' => 'web', 'name' => 'access-tokens-ver']);
        Permission::create(['guard_name' => 'web', 'name' => 'access-tokens-crear']);
        Permission::create(['guard_name' => 'web', 'name' => 'access-tokens-borrar']);

        $role = Role::create([
            'guard_name' => 'web',
            'name' => 'Superadmin'
        ]);

        $role->givePermissionTo([
            'usuarios-editar',
            'usuarios-borrar',
            'usuarios-ver',
            'permisos-ver',
            'roles-ver',
            'access-tokens-ver',
            'access-tokens-crear',
            'access-tokens-borrar'
        ]);
    }
}
