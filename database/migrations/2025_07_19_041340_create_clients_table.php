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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('Nombre del cliente');
             $table->enum('type', ['empresa', 'individual', 'gobierno', 'ong'])->comment('Tipo de cliente: persona o empresa');
            $table->string('email')->unique()->comment('Correo electrónico del cliente');
            $table->string('phone')->nullable()->comment('Teléfono del cliente');

            // Relaciones
            $table->foreignId('user_count_id')->nullable()->constrained('users')->onDelete('set null')->comment('ID del usuario asociado al cliente');

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
        Schema::dropIfExists('clients');
    }
};
