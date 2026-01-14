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
        Schema::create('reward_codes', function (Blueprint $table) {
            $table->string('code', 16)->primary();
            $table->foreignId('commercial_good_id')->constrained('commercial_goods');
            $table->uuid('batch_id');
            $table->enum('status', ['generated', 'active', 'used', 'void'])->default('generated');
            $table->foreignId('user_id')->nullable()->constrained('tenant_users');
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_codes');
    }
};
