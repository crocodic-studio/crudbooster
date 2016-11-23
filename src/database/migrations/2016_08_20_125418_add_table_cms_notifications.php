<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCmsNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('id_cms_users')->nullable();
            $table->string('content')->nullable();
            $table->string('url')->nullable();
            $table->boolean('is_read')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cms_notifications');
    }
}
