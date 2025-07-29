<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding roles...');

        // Super admin: gestiona toda la app
        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            // 'guard_name' => 'web' // Asegúrate de incluir esto
        ]);
        // Asigna permisos al rol super_admin

        // Obtener todos los permisos existentes
        $permissions = Permission::all();

        // Sincronizar todos los permisos con el rol (esto eliminará los permisos anteriores)
        $superAdmin->syncPermissions($permissions);

        // Cliente: puede ver y gestionar sus propios tickets
        Role::firstOrCreate([
            'name' => 'Cliente',
            // 'guard_name' => 'web' // Asegúrate de incluir esto
        ]);

        // Desarrollador: gestiona los tickets y clientes
        Role::firstOrCreate([
            'name' => 'Desarrollador',
            // 'guard_name' => 'web' // Asegúrate de incluir esto
        ]);
    }
}
