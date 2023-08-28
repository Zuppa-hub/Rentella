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
        Schema::create('admin', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("surname");
            $table->string("email");
            $table->string("adress");
            $table->string("phone_number");
            $table->timestamps();
        });
        Schema::create('user', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("surname");
            $table->string("email");
            $table->timestamps();
        });
        Schema::create('beach', function (Blueprint $table) {
            $table->id();
            $table->string("owner_id");
            $table->string("name");
            $table->string("location_id");
            $table->string("description");
            $table->string("opening_date");
            $table->string("special_note");
            $table->string("latitude");
            $table->string("longitude");
            $table->boolean("allowed_animals");
            $table->timestamps();
        });
        Schema::create('BeachZone', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->float("price");
            $table->string("description");
            $table->string("special_note");
            $table->string("latitude");
            $table->string("longitude");
            $table->string("beach_id");
            $table->timestamps();
        });
        Schema::create('umbrellas',function (Blueprint $table){
            $table->id();
            $table->string("zone_id");
            $table->integer("number");
            $table->string("description");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
