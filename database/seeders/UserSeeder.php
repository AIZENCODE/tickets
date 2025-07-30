<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Artisan;
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
            'email' => 'aizencode',
            'password' => bcrypt('12345678'),
        ]);
        $userSuperAdmin->assignRole('super_admin'); // Asigna la instancia del rol

        // Define un array de usuarios
        $users = [
            [
                'name' => 'Michael Orizano Zevallos',
                'email' => 'Morizano',
                'password' => bcrypt('12345678')
            ],
            [
                'name' => 'Diego Saravia',
                'email' => 'dsaravia',
                'password' => bcrypt('12345678')
            ],
            [
                'name' => 'Carlos Cruces Padilla',
                'email' => 'ccruces',
                'password' => bcrypt('12345678')
            ],
            [
                'name' => 'Danna Cervantes Quispe',
                'email' => 'Dcervantes',
                'password' => bcrypt('12345678')
            ],
            [
                'name' => 'Jorge Luis',
                'email' => 'jluis2',
                'password' => bcrypt('12345678')
            ]
            // Agrega más usuarios según sea necesario
        ];

        // Itera sobre el array de usuarios y crea cada usuario
        foreach ($users as $userData) {
            $user = User::create($userData);
            $user->assignRole('Desarrollador'); // Asigna el rol de Desarrollador
        }

        // 3. Crea el usuario cliente y asígnale el rol
        $userClient = User::create([
            'name' => 'Casablanca',
            'email' => 'Casablanca',
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

        // 3. Crea el usuario cliente y asígnale el rol
        $userClientwo = User::create([
            'name' => 'Diamond',
            'email' => 'Diamond',
            'password' => bcrypt('12345678'),
        ]);

        $userClientwo->assignRole('Cliente'); // Asigna la instancia del rol
        // 4. Crea el cliente asociado
        Client::create([
            'name' => 'Diamond',
            'type' => 'empresa',
            'email' => 'Diamond',
            'phone' => '936148456',
            'user_count_id' => $userClientwo->id,
            'user_id' => 1,
        ]);
    }
}
