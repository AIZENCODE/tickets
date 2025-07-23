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
        Schema::create('ticket_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade')->comment('ID del ticket asociado al usuario');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('ID del usuario asociado al ticket');


            $table->boolean('is_responsible')->default(false)->comment('Indica si el usuario es el responsable del ticket');
            $table->unique(['ticket_id', 'user_id'], 'ticket_user_unique'); // Asegura que un usuario no pueda estar asociado al mismo ticket más de una vez

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_user');
    }
};
