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
        Schema::create('user', function (Blueprint $table) {
            $table->integer('userID')->autoIncrement(); // Primary Key
            $table->integer('agID')->nullable();
            $table->integer('cpt')->nullable();
            $table->string('nprenom', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('mdp', 40)->nullable();
            $table->string('pays', 7)->nullable();
            $table->decimal('solde', 10, 2)->unsigned()->default(0);
            $table->string('activationDATE', 32)->nullable();
            $table->string('ville', 128)->nullable();
            $table->string('identId', 20)->nullable();
            $table->string('fname', 100)->nullable();
            $table->decimal('rate', 3, 2)->nullable();
            $table->integer('balance')->nullable();
            $table->string('statut', 2)->nullable();
            $table->string('socialId', 35)->nullable();
            $table->decimal('ratemc', 3, 2)->nullable();
            $table->string('refcode', 6)->nullable();
            $table->decimal('comm', 8, 2)->nullable();
            $table->string('refmaster', 6)->nullable();
            $table->string('regular_otp', 16)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
