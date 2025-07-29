<?php

namespace Database\Seeders;

use App\Models\Ticket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $this->command->info('Seeding tickets...');

        Ticket::create([
            'title' => 'Sample Ticket',
            'description' => 'This is a sample ticket description.',
            'status' => 'open',
            'priority' => 'medium',
            'client_id' => 1, // Assuming a client with ID 1 exists
            'designation_id' => 1, // Assuming a project or subproject with ID 1 exists
            'designation_type' => 'App\Models\Project', // or 'subproject'
        ]);
        Ticket::create([
            'title' => 'Another Ticket',
            'description' => 'This is another ticket description.',
            'status' => 'in_progress',
            'priority' => 'high',
            'client_id' => 1, // Assuming a client with ID 1 exists
            'designation_id' => 2, // Assuming a project or subproject with ID 2 exists
            'designation_type' => 'App\Models\SubProject', // or 'project'
        ]);

    }
}
