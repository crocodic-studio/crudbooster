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
            $table->dateTime('send_at');
            $table->string('email_recipient');
            $table->string('email_cc');
            $table->string('email_subject');
            $table->text('email_content');
            $table->boolean('is_sent');            
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
