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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            $table->text('content')->comment('Contenido del comentario');

            // Relaciones
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade')->comment('ID del ticket relacionado con el comentario');
            $table->foreignId('user_acount')->constrained('users')->onDelete('cascade')->comment('ID del usuario que creó el comentario');

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
        Schema::dropIfExists('comments');
    }
};
