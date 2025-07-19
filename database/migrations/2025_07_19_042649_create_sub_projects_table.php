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
        Schema::create('sub_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nombre del subproyecto');
            $table->text('description')->nullable()->comment('Descripción del subproyecto');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade')->comment('ID del proyecto al que pertenece el subproyecto');

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
        Schema::dropIfExists('sub_projects');
    }
};
