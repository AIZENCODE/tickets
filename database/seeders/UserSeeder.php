<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $this->command->info('Seeding users...');

        // 2. Crea el usuario admin
        $userSuperAdmin = User::create([
            'name' => 'Diego Saravia',
            'email' => 'migelo5511@gmail.com',
            'password' => bcrypt('12345678'),
        ]);
        $userSuperAdmin->assignRole('super_admin'); // Asigna la instancia del rol

        // 3. Crea el usuario desarrollador y asígnale el rol
        $userDesarrollador = User::create([
            'name' => 'Desarrollador',
            'email' => 'orizano@gmail.com',
            'password' => bcrypt('12345678'),
        ]);
        $userDesarrollador->assignRole('Desarrollador'); // Asigna la instancia del rol

        // 3. Crea el usuario cliente y asígnale el rol
        $userClient = User::create([
            'name' => 'Casablanca',
            'email' => 'casablanca@gmail.com',
            'password' => bcrypt('12345678'),
        ]);
        $userClient->assignRole('Cliente'); // Asigna la instancia del rol

        // 4. Crea el cliente asociado
        Client::create([
            'name' => 'Casablanca',
            'type' => 'empresa',
            'email' => 'casablanca@gmail.com',
            'phone' => '936148456',
            'user_count_id' => $userClient->id,
            'user_id' => 1,
        ]);
    }
}
