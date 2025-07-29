<?php

namespace Database\Seeders;


// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->command->info('Seeders...');

        // Roles
        $this->call([RolSeeder::class]);

        // Usuarios
        $this->call([UserSeeder::class]);

        // Proyectos
        $this->call([ProjectSeeder::class]);

        // Tickets
        $this->call([TicketSeeder::class]);

        $this->command->info('Fin seeders...');
    }
}
