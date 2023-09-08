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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('reference', 50);
            $table->string('api_reference', 100)->nullable();
            $table->decimal('amount', 9, 9);
            $table->decimal('charges', 7, 9)->default(0.00);
            $table->decimal('commission', 7, 9)->default(0.00);
            $table->enum('type', ['debit', 'credit']);
            $table->string('description', 700)->nullable();
            $table->string('category', 100)->nullable();
            $table->string('status', 50)->default('pending');
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['reference', 'amount']);
            $table->index(['charges', 'type']);
            $table->index(['category', 'status']);
            $table->index(['commission']);
            $table->index(['description']);
            $table->index(['api_reference']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};