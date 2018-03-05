<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCmsEmailQueuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_email_queues', function(Blueprint $table)
		{
			$table->increments('id');
			$table->dateTime('send_at')->nullable();
			$table->string('email_recipient')->nullable();
			$table->string('email_from_email', 50)->nullable();
			$table->string('email_from_name', 50)->nullable();
			$table->string('email_cc_email', 50)->nullable();
			$table->string('email_subject', 100)->nullable();
			$table->text('email_content', 65535)->nullable();
			$table->text('email_attachments', 65535)->nullable();
			$table->boolean('is_sent')->nullable();
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
		Schema::drop('cms_email_queues');
	}

}
