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

        // Obtener todos los permisos existentes
        $permissions = Permission::all();

        // Sincronizar todos los permisos con el rol (esto eliminará los permisos anteriores)
        $superAdmin->syncPermissions($permissions);

          // Obtener todos los permisos existentes
        $permissions = Permission::all();
        $this->command->info('Permissions retrieved: ' . $permissions->count());

        // Listar los permisos para verificar
        foreach ($permissions as $permission) {
            $this->command->info('- ' . $permission->name);
        }


        // Cliente: puede ver la lista de proyectos, ver proyectos individualmente, y gestionar sus tickets
        $cliente = Role::firstOrCreate(['name' => 'Cliente']);
        $clientePermissions = [
            'view_project',
            'view_any_project',
            'view_ticket',
            'view_any_ticket',
            'create_ticket',
            'update_ticket',
        ];

        // Sincronizar permisos con el rol de Cliente
        $cliente->syncPermissions($clientePermissions);

        // Desarrollador: gestiona los tickets y clientes
        Role::firstOrCreate([
            'name' => 'Desarrollador',
            // 'guard_name' => 'web' // Asegúrate de incluir esto
        ]);

        // Administrador: gestiona los tickets y clientes
        Role::firstOrCreate([
            'name' => 'Administrador',
            // 'guard_name' => 'web' // Asegúrate de incluir esto
        ]);

        // Asignador: asigna tickets a desarrolladores
        Role::firstOrCreate([
            'name' => 'Asignador',
            // 'guard_name' => 'web' // Asegúrate de incluir esto
        ]);
    }
}
