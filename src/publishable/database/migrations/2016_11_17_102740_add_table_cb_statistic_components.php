<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableStatisticComponents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_statistic_components', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cb_statistics_id')->nullable();
            $table->string('component_id')->nullable();
            $table->string('component_name')->nullable();
            $table->string('area_name',55)->nullable();
            $table->integer('sorting')->nullable();
            $table->string('name')->nullable();
            $table->longtext('config')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cb_statistic_components');
    }
}
