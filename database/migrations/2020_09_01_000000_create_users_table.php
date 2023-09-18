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
        Schema::dropIfExists('users');
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name', 50)->index();
            $table->string('last_name', 50)->index();
            $table->string('middle_name', 50)->nullable()->index();
            $table->string('business_name', 150)->nullable()->index();
            $table->string('email', 80)->nullable()->unique();
            $table->string('phone', 20)->unique();
            $table->string('bvn', 11)->nullable();
            $table->string('account_number', 11)->nullable()->unique();
            $table->decimal('withdrawal_limit', 19, 8)->default(0);
            $table->string('role', 30)->default('customer')->index();
            $table->string('verification_token')->nullable()->index();
            $table->timestamp('account_verified_at')->nullable();
            $table->enum('is_verified', ['yes', 'no'])->default('no')->index();
            $table->enum('account_type', ['ordinary', 'classic', 'premium', 'super'])->default('ordinary')->index();
            $table->string('transaction_pin')->nullable();
            $table->enum('account_status', ['unverified', 'verified', 'suspended', 'banned'])->default('unverified')->index();
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
