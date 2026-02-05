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
        Schema::create('transaction', function (Blueprint $table) {
           $table->id('transID'); // Primary Key
            $table->integer('userID')->index(); 
            $table->decimal('amount', 10, 2);
            $table->string('status', 20)->default('pending');
            $table->dateTime('date'); 
            $table->string('reference', 50)->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
