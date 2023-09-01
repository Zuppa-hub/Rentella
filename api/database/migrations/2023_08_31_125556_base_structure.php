<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('owners', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("surname");
            $table->string("email");
            $table->string("adress");
            $table->string("phone_number");
            $table->timestamps();
        });
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("surname");
            $table->string("email");
            $table->timestamps();
        });
        Schema::create('cities_location', function (Blueprint $table) {
            $table->id();
            $table->string("city_name");
            $table->float("latitude");
            $table->float("longitude");
            $table->string("description");
            $table->timestamps();
        });
        Schema::create('opening_dates', function (Blueprint $table) {
            $table->id();
            $table->dateTime("start_date");
            $table->dateTime("end_date");
            //$table->foreignId("beach_id")->constrained('beaches');
            $table->timestamps();
        });
        Schema::create('beach_types', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->timestamps();
        });
        Schema::create('beaches', function (Blueprint $table) {
            $table->id();
            $table->foreignId("owner_id")->constrained('owners'); // Non utilizzare 'change()' qui
            $table->string("name");
            $table->foreignId("location_id")->constrained('cities_location'); // Correggi qui
            $table->string("description");
            $table->foreignId("opening_date_id")->constrained('opening_dates');
            $table->string("special_note");
            $table->float("latitude");
            $table->float("longitude");
            $table->boolean("allowed_animals");
            $table->foreignId("type_id")->constrained('beach_types');
            $table->timestamps();
        });
        
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('beach_zones', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->foreignId("price_id")->constrained('prices');
            $table->string("description");
            $table->string("special_note");
            $table->float("latitude");
            $table->float("longitude");
            $table->foreignId("beach_id")->constrained('beaches');
            $table->timestamps();
        });
        Schema::create('umbrellas', function (Blueprint $table) {
            $table->id();
            $table->foreignId("zone_id")->constrained('beach_zones');
            $table->integer("number");
            $table->string("special_note");
            $table->timestamps();
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId("umbrella_id")->constrained('umbrellas');
            $table->dateTime("start_date");
            $table->dateTime("end_date");
            $table->foreignId("user_id")->constrained('users');
            $table->foreignId("price_id")->constrained('prices');
            $table->timestamps();
        });
        
        Schema::create('beach_pictures', function (Blueprint $table) {
            $table->id();
            $table->string("photo");
            $table->foreignId("beach_id")->constrained('beaches');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraints if needed in the down method
        Schema::dropIfExists('owners');
        Schema::dropIfExists('users');
        Schema::dropIfExists('cities_location');
        Schema::dropIfExists('beaches');
        Schema::dropIfExists('beach_zones');
        Schema::dropIfExists('umbrellas');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('opening_dates');
        Schema::dropIfExists('beach_types');
        Schema::dropIfExists('prices');
        Schema::dropIfExists('beach_pictures');
    }
};
