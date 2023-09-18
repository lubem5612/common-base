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
        Schema::create('supports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('title')->index();
            $table->text('content')->nullable();
            $table->string('type', 70)->default('FAILED_TRANSACTION')->index(); //ACCOUNT_UPGRADE, FAILED_TRANSACTION, AUTH_ISSUES
            $table->string('status', 30)->default('opened')->index(); //closed, archived, opened,
            $table->string('file', 700)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supports');
    }
};
