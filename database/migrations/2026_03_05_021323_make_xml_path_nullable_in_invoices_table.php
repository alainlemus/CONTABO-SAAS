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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('xml_path')->nullable()->change();
            $table->date('fecha_emision')->nullable()->change();
            $table->string('rfc_emisor', 13)->nullable()->change();
            $table->string('rfc_receptor', 13)->nullable()->change();
            $table->decimal('subtotal', 14, 2)->nullable()->default(0)->change();
            $table->decimal('descuento', 14, 2)->nullable()->default(0)->change();
            $table->decimal('iva', 14, 2)->nullable()->default(0)->change();
            $table->decimal('isr_retenido', 14, 2)->nullable()->default(0)->change();
            $table->decimal('iva_retenido', 14, 2)->nullable()->default(0)->change();
            $table->decimal('total', 14, 2)->nullable()->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('xml_path')->nullable(false)->change();
            $table->date('fecha_emision')->nullable(false)->change();
            $table->string('rfc_emisor', 13)->nullable(false)->change();
            $table->string('rfc_receptor', 13)->nullable(false)->change();
            $table->decimal('subtotal', 14, 2)->nullable(false)->default(0)->change();
            $table->decimal('descuento', 14, 2)->nullable(false)->default(0)->change();
            $table->decimal('iva', 14, 2)->nullable(false)->default(0)->change();
            $table->decimal('isr_retenido', 14, 2)->nullable(false)->default(0)->change();
            $table->decimal('iva_retenido', 14, 2)->nullable(false)->default(0)->change();
            $table->decimal('total', 14, 2)->nullable(false)->default(0)->change();
        });
    }
};
