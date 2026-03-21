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
        $driver = Schema::getConnection()->getDriverName();

        Schema::table('opening_dates', function (Blueprint $table) use ($driver) {
            if (Schema::hasColumn('opening_dates', 'beach_id')) {
                if ($driver !== 'sqlite') {
                    $table->dropForeign(['beach_id']);
                }

                $table->dropColumn('beach_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opening_dates', function (Blueprint $table) {
            $table->foreignId('beach_id')->constrained('beaches');
        });
    }
};
