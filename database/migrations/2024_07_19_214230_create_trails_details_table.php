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
        Schema::create('trails_details', function (Blueprint $table) {
            $table->integer('trail_id')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->geometry('points')->nullable();
            $table->json('properties')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trails_details');
    }
};
