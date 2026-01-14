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
        Schema::create('tenant_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();
            $table->string('phone')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('name')->nullable();
            $table->date('dob')->nullable();
            $table->integer('points_balance')->default(0);
            $table->integer('lifetime_points')->default(0);
            $table->string('current_rank_key')->default('member');
            $table->string('referral_code')->unique();
            $table->bigInteger('referred_by_id')->unsigned()->nullable();
            $table->json('signals')->nullable();
            $table->boolean('marketing_opt_in')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('referred_by_id')->references('id')->on('tenant_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_users');
    }
};
