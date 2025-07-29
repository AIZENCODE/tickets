<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Subproject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $this->command->info('Seeding projects...');

        Project::create([
            'name' => 'Agrojabas',
            'description' => 'Este es un proyecto de ejemplo para demostrar la funcionalidad del sistema.',
            'client_id' => 1, // Asegúrate de que el cliente con ID 1 exista
            'user_id' => 1, // Asegúrate de que el usuario con ID 1 exista
        ]);
        Project::create([
            'name' => 'Agroquimicos',
            'description' => 'Este es un proyecto de prueba para verificar la funcionalidad del sistema.',
            'client_id' => 1, // Asegúrate de que el cliente con ID 1 exista
            'user_id' => 1, // Asegúrate de que el usuario con ID 1 exista
        ]);
        Project::create([
            'name' => 'Topico',
            'description' => 'Este es un proyecto de demostración para ilustrar la funcionalidad del sistema.',
            'client_id' => 1, // Asegúrate de que el cliente con ID 1 exista
            'user_id' => 1, // Asegúrate de que el usuario con ID 1 exista
        ]);

        Subproject::create([
            'name' => 'Subproyecto 1',
            'description' => 'Este es un subproyecto de ejemplo para demostrar la funcionalidad del sistema.',
            'project_id' => 1, // Asegúrate de que el proyecto con ID 1 exista
            'user_id' => 1, // Asegúrate de que el usuario con ID 1 exista
        ]);


    }
}
