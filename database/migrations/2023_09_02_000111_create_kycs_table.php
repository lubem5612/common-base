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
        Schema::dropIfExists('kycs');
        Schema::create('kycs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('image_url', 600)->nullable();
            $table->string('identity_card_url', 600)->nullable();
            $table->string('address_proof_url', 600)->nullable();
            $table->string('identity_type')->nullable();
            $table->string('identity_card_number')->nullable()->index();
            $table->foreignId('country_of_origin_id')->nullable()->constrained('countries')->cascadeOnDelete();
            $table->foreignId('country_of_residence_id')->nullable()->constrained('countries')->cascadeOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('states')->cascadeOnDelete();
            $table->foreignId('lga_id')->nullable()->constrained('lgas')->cascadeOnDelete();
            $table->string('city', 60)->nullable()->index();
            $table->string('next_of_kin', 100)->nullable()->index();
            $table->string('next_of_kin_contact', 16)->nullable();
            $table->string('mother_maiden_name', 80)->nullable();
            $table->string('residential_status', 100)->nullable()->index();
            $table->string('employment_status', 100)->nullable()->index();
            $table->string('employer', 400)->nullable()->index();
            $table->string('job_title', 150)->nullable()->index();
            $table->string('educational_qualification', 150)->nullable()->index();
            $table->timestamp('date_of_employment')->nullable();
            $table->integer('number_of_children')->nullable()->index();
            $table->string('income_range', 150)->nullable()->index();
            $table->enum('verification_status', ['verified', 'incomplete', 'unverified'])->default('unverified')->index();
            $table->enum('is_loan_compliant', ['no', 'yes'])->default('no')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kycs');
    }
};