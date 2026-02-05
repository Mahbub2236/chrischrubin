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
        Schema::create('compte', function (Blueprint $table) {
           $table->integer('compteId')->autoIncrement(); // Primary Key
            $table->integer('userId')->index(); // Links to User table
            $table->string('pays')->nullable(); // Country code
            $table->string('banque')->nullable(); // Bank code
            $table->string('type')->nullable(); // Account type
            $table->string('monnaie')->nullable(); // Currency (e.g., USD, HTG)
            $table->string('numero', 40)->unique(); // Account number
            $table->string('routing', 10)->nullable(); // Routing number
            $table->dateTime('date')->nullable();
            $table->string('lastdigit', 4)->nullable(); // Last 4 digits of card/account
            $table->string('expiration', 4)->nullable(); // Expiry date
            $table->string('compte', 16)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compte');
    }
};
