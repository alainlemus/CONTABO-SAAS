<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiscal_obligations', function (Blueprint $table): void {
            $table->string('acuse_pdf_path')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('fiscal_obligations', function (Blueprint $table): void {
            $table->dropColumn('acuse_pdf_path');
        });
    }
};
