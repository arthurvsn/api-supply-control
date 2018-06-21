<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuplliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supllies', function (Blueprint $table) {
            $table->increments('id');

            $table->decimal('liters', 5, 2);
            $table->double('amount');
            $table->double('fuel_price');
            $table->enum('type', ['ALCOHOL', 'GASOLINE', 'DIESEL', 'GAS'])->default('GASOLINE');
            $table->dateTime('date_supply');

            $table->integer('car_id')->unsigned();
            $table->foreign('car_id')->references('id')->on('cars');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supllies');
    }
}
