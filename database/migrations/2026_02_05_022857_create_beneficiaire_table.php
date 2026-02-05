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
        Schema::create('beneficiaire', function (Blueprint $table) {
            $table->integer('beneficiaireId')->autoIncrement(); // Primary Key
            $table->integer('userId')->index(); // Foreign Key connection
            $table->integer('benId')->index();
            $table->dateTime('date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaire');
    }
};
