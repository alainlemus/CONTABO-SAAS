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
        Schema::table('users', function (Blueprint $table) {
            // El rol del usuario dentro de su despacho
            $table->enum('role', ['admin', 'capturista', 'viewer'])->default('admin')->after('email');

            // owner_id apunta al admin dueño de la cuenta.
            // Los admins tienen owner_id = null.
            // Capturistas y viewers tienen owner_id = id del admin que los creó.
            $table->foreignId('owner_id')
                ->nullable()
                ->after('role')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropColumn(['role', 'owner_id']);
        });
    }
};
