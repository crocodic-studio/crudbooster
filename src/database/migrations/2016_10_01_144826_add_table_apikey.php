<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddTableApikey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_apikey', function (Blueprint $table) {
            $table->increments('id');

            $table->string('screetkey')->nullable();
            $table->integer('hit')->nullable();
            $table->string('status', 25)->default('active');

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
        Schema::drop('cms_apikey');
    }
}
