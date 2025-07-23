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
        Schema::create('subproject_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subproject_id')->constrained('sub_projects')->onDelete('cascade')->comment('ID del subproyecto asociado al usuario');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('ID del usuario asociado al subproyecto');

            $table->boolean('is_responsible')->default(false)->comment('Indica si el usuario es el responsable del subproyecto');
            $table->unique(['subproject_id', 'user_id'], 'subproject_user_unique'); // Asegura que un usuario no pueda estar asociado al mismo subproyecto más de una vez

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subproject_user');
    }
};
