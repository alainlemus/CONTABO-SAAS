<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiscal_obligations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();

            // Tipo de obligación: isr_mensual, iva_mensual, isr_anual, diot, imss_bimestral, etc.
            $table->string('type', 30);

            // Período al que corresponde la obligación
            $table->smallInteger('period_year');
            $table->tinyInteger('period_month')->nullable(); // null = obligación anual

            // Fecha límite oficial de presentación ante el SAT
            $table->date('due_date');

            // Gestión del cumplimiento
            $table->string('status', 20)->default('pending'); // pending | presented | not_applicable | overdue
            $table->date('presented_at')->nullable();
            $table->string('reference', 100)->nullable(); // Número de acuse / referencia SAT
            $table->text('notes')->nullable();

            $table->timestamps();

            // Evita duplicados: un cliente no puede tener dos veces la misma obligación para el mismo período
            $table->unique(['client_id', 'type', 'period_year', 'period_month'], 'fiscal_obligations_unique');

            $table->index(['client_id', 'status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiscal_obligations');
    }
};
