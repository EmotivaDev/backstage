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
        Schema::create('trips', function (Blueprint $table) {
            $table->integer('deviceid')->unique();
            $table->integer('number')->nullable();
            $table->string('origin')->nullable();
            $table->string('destination')->nullable();
            $table->string('draft')->nullable();
            $table->string('loadtype')->nullable();
            $table->string('tonnes')->nullable();
            $table->string('bargues')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
