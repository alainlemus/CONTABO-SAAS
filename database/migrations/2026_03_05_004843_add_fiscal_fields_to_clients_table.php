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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('person_type')->default('moral')->after('tax_id');        // fisica | moral
            $table->string('tax_regime', 10)->nullable()->after('person_type');      // clave SAT: 601, 612, 626, etc.
            $table->string('curp', 18)->nullable()->after('tax_regime');             // solo persona física
            $table->string('legal_rep_name')->nullable()->after('curp');             // representante legal
            $table->string('legal_rep_rfc', 13)->nullable()->after('legal_rep_name');
            $table->string('economic_activity')->nullable()->after('legal_rep_rfc'); // giro / actividad
            $table->string('scian_code', 10)->nullable()->after('economic_activity');
            $table->unsignedSmallInteger('employee_count')->nullable()->after('scian_code');
            $table->date('relationship_started_at')->nullable()->after('employee_count');
            $table->string('obligations_periodicity')->nullable()->after('relationship_started_at'); // mensual | bimestral | anual
            $table->string('efirma_cer_path')->nullable()->after('obligations_periodicity');
            $table->string('efirma_key_path')->nullable()->after('efirma_cer_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'person_type',
                'tax_regime',
                'curp',
                'legal_rep_name',
                'legal_rep_rfc',
                'economic_activity',
                'scian_code',
                'employee_count',
                'relationship_started_at',
                'obligations_periodicity',
                'efirma_cer_path',
                'efirma_key_path',
            ]);
        });
    }
};
