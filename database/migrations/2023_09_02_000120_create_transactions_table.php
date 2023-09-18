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
            $table->decimal('amount', 19, 8);
            $table->decimal('charges', 19, 8)->default(0);
            $table->decimal('commission', 19, 8)->default(0);
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
