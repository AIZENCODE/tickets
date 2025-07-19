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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('Nombre del proyecto');
            $table->text('description')->nullable()->comment('Descripción del proyecto');

            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade')->comment('ID del cliente asociado al proyecto');


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
        Schema::dropIfExists('projects');
    }
};
