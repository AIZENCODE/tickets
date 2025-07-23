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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->string('title')->comment('Título del ticket');
            $table->enum('type', ['bug', 'feature_request', 'support'])->default('support')->comment('Tipo de ticket: bug, solicitud de característica o soporte');
            $table->text('description')->comment('Descripción del ticket');
            $table->enum('status', ['open', 'in_progress', 'stop', 'closed'])->default('open')->comment('Estado del ticket: abierto, en progreso, cerrado');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->comment('Prioridad del ticket: baja, media, alta');


            $table->date('start_date')->nullable()->comment('Fecha de inicio del ticket');
            $table->date('end_date')->nullable()->comment('Fecha de finalización del ticket');
            $table->timestamp('closed_at')->nullable()->comment('Fecha y hora de cierre del ticket');

            // Relaciones
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade')->comment('ID del cliente relacionado con el ticket');
            // Relación polimórfica (puede ser proyecto o subproyecto)
            $table->unsignedBigInteger('designation_id')->comment('ID del proyecto o subproyecto');
            $table->string('designation_type')->comment('Tipo: project o subproject');

            // Auditoria
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('ID del usuario que creó el cliente');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null')->comment('ID del usuario que actualizó el cliente');

            $table->timestamps();
            $table->softDeletes()->comment('Marca de tiempo para eliminar el ticket de forma suave (soft delete)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
