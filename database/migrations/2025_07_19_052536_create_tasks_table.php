<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->string('title')->comment('Título de la tarea');
            $table->text('description')->nullable()->comment('Descripción de la tarea');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending')->comment('Estado de la tarea: pendiente, en progreso o completada');

            // Relaciones
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade')->comment('ID del ticket relacionado con la tarea');

            // Auditoria
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('ID del usuario que creó el cliente');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null')->comment('ID del usuario que actualizó el cliente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
