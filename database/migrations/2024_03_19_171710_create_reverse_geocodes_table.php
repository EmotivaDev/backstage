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
        Schema::create('reverse_geocodes', function (Blueprint $table) {
            $table->id();
            $table->string('location');
            $table->float('milemark');
            $table->float('latitude');
            $table->float('longitude');
            $table->float('maxspeed')->nullable();
            $table->float('maxrpm')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reverse_geocodes');
    }
};
