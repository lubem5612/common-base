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
        Schema::create('debit_cards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('first_digits', 10)->nullable()->index();
            $table->string('last_digits', 10)->nullable()->index();
            $table->string('issuer', 400)->nullable()->index();
            $table->string('email')->index();
            $table->string('type', 50)->nullable()->index();
            $table->string('country', 150)->nullable();
            $table->string('currency', 10)->nullable();
            $table->string('expiry', 20)->index();
            $table->string('token', 300)->nullable()->index();
            $table->enum('is_third_party', ['no', 'yes'])->default('no')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debit_cards');
    }
};
