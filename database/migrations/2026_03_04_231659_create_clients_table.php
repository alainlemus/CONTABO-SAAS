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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tax_id');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->default('MX');
            $table->string('postal_code', 12)->nullable();
            $table->string('status')->default('active');
            $table->string('billing_cycle')->default('monthly');
            $table->text('onboarding_notes')->nullable();
            $table->timestamp('onboarding_completed_at')->nullable();
            $table->string('portal_sat_user')->nullable();
            $table->string('portal_sat_password')->nullable();
            $table->json('documents')->nullable();
            $table->string('compliance_level')->default('medium');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
