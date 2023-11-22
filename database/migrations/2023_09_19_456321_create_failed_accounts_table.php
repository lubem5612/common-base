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
        Schema::create('failed_accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid('reference_id')->unique();
            $table->string('phone', 50)->nullable()->index();
            $table->string('email', 100)->nullable()->index();
            $table->json('data_dump')->nullable();
            $table->json('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_accounts');
    }
};
