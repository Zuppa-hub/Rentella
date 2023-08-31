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
        Schema::create('location', function (Blueprint $table) {
            $table->id();
            $table->string("city_name");
            $table->integer("latitude");
            $table->integer("longitude");
        });
        Schema::create('beach', function (Blueprint $table) {
            $table->id();
            $table->foreignId("owner_id")->unsigned(); // Non utilizzare 'change()' qui
            $table->string("name");
            $table->foreignId("location_id")->constrained('location'); // Correggi qui
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
            $table->foreignId("beach_id")->constrained('beach');
            $table->timestamps();
        });
        Schema::create('umbrellas', function (Blueprint $table) {
            $table->id();
            $table->foreignId("zone_id")->constrained(
                table: 'BeachZone',
                indexName: 'id'
            );
            $table->integer("number");
            $table->string("description");
        });

        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->foreignId("umbrella_id")->constrained('umbrellas');
            $table->dateTime("startdate");
            $table->dateTime("enddate");
            $table->foreignId("user_id")->constrained('user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraints if needed in the down method
    }
};