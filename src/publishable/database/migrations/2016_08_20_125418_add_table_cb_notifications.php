<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCbNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('users_id')->nullable();
            $table->string('content')->nullable();
            $table->string('url')->nullable();
            $table->boolean('is_read')->nullable();

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
        Schema::drop('cb_notifications');
    }
}
