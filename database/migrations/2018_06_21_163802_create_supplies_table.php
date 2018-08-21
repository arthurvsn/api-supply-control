<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplies', function (Blueprint $table) {
            $table->increments('id');

            $table->decimal('liters', 5, 2);
            $table->decimal('amount', 5, 2);
            $table->double('fuel_price');
            $table->date('date_supply');

            $table->integer('car_id')->unsigned();
            $table->foreign('car_id')->references('id')->on('cars');

            $table->integer('type_fuel_id')->unsigned();
            $table->foreign('type_fuel_id')->references('id')->on('type_fuels');

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
        Schema::dropIfExists('supplies');
    }
}
