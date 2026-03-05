<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();

            // Tipo: ingreso (emitida por el cliente) o gasto (recibida por el cliente)
            $table->enum('type', ['ingreso', 'gasto']);

            // Datos extraídos del XML CFDI
            $table->string('uuid', 36)->nullable()->unique();
            $table->string('serie', 25)->nullable();
            $table->string('folio', 40)->nullable();
            $table->date('fecha_emision')->nullable();
            $table->string('rfc_emisor', 13)->nullable();
            $table->string('nombre_emisor')->nullable();
            $table->string('rfc_receptor', 13)->nullable();
            $table->string('nombre_receptor')->nullable();
            $table->string('uso_cfdi', 10)->nullable();
            $table->enum('tipo_comprobante', ['I', 'E', 'T', 'N', 'P'])->default('I');
            $table->string('metodo_pago', 5)->nullable();
            $table->string('forma_pago', 5)->nullable();
            $table->string('moneda', 10)->default('MXN');
            $table->decimal('subtotal', 14, 2)->nullable()->default(0);
            $table->decimal('descuento', 14, 2)->nullable()->default(0);
            $table->decimal('iva', 14, 2)->nullable()->default(0);
            $table->decimal('isr_retenido', 14, 2)->nullable()->default(0);
            $table->decimal('iva_retenido', 14, 2)->nullable()->default(0);
            $table->decimal('total', 14, 2)->nullable()->default(0);
            $table->string('concepto_principal')->nullable();

            // Archivos
            $table->string('xml_path')->nullable();
            $table->string('pdf_path')->nullable();

            // Control
            $table->enum('status', ['pendiente', 'procesado', 'error'])->default('pendiente');
            $table->text('parse_error')->nullable();

            $table->timestamps();

            $table->index(['client_id', 'type', 'fecha_emision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
