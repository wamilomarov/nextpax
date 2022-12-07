<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();

            $table->string('property_id', 36);
            $table->integer('duration');
            $table->integer('amount');
            $table->string('currency');
            $table->string('persons');
            $table->string('weekdays');
            $table->integer('minimum_stay');
            $table->integer('maximum_stay');
            $table->integer('extra_person_price');
            $table->string('extra_person_price_currency');
            $table->date('period_from');
            $table->date('period_till');
            $table->integer('version');
        });
    }

    public function down()
    {
        Schema::dropIfExists('prices');
    }
};
