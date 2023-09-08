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
        Schema::dropIfExists('countries');
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5)->nullable();
            $table->string('name', 80);
            $table->string('continent', 80)->nullable();

            $table->index(['code', 'name']);
            $table->index(['continent']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
