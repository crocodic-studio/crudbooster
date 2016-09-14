<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCmsEmailQueues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_email_queues', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->dateTime('send_at')->nullable();
            $table->string('email_recipient')->nullable();
            $table->string('email_cc')->nullable();
            $table->string('email_subject')->nullable();
            $table->text('email_content')->nullable();
            $table->boolean('is_sent')->nullable();            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cms_email_queues');
    }
}
