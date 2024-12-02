<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        if (Schema::hasColumn('tc_positions', 'trip')) {
            Schema::table('tc_positions', function (Blueprint $table) {
                $table->dropColumn('trip');
            });
        }

        Schema::table('tc_positions', function (Blueprint $table) {
            DB::unprepared('ALTER TABLE tc_positions 
            CHANGE COLUMN `devicetime` `devicetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
            CHANGE COLUMN `fixtime` `fixtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;
            ');
             $table->string('trip', 4000)->nullable()->after('geofenceids');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
