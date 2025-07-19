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
        Schema::create('project_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade')->comment('ID del proyecto asociado al usuario');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('ID del usuario asociado al proyecto');

            $table->boolean('is_responsible')->default(false)->comment('Indica si el usuario es el responsable del proyecto');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_user');
    }
};
