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
        Schema::create('task_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade')->comment('ID de la tarea asociada al usuario');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('ID del usuario asociado a la tarea');

            $table->boolean('is_responsible')->default(false)->comment('Indica si el usuario es el responsable del ticket');

            $table->unique(['task_id', 'user_id'], 'task_user_unique'); // Asegura que un usuario no pueda estar asociado a la misma tarea más de una vez
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_user');
    }
};
