<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();

            $table->string('property_id', 36);
            $table->date('date');
            $table->integer('quantity');
            $table->boolean('arrival_allowed');
            $table->boolean('departure_allowed');
            $table->integer('minimum_stay');
            $table->integer('maximum_stay');
            $table->integer('version');

        });
    }

    public function down()
    {
        Schema::dropIfExists('availabilities');
    }
};
