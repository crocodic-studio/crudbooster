<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCmsLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->string('ipaddress',50);
			$table->string('useragent');
			$table->string('url');
			$table->string('description');
			$table->integer('id_cms_users');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cms_logs');
	}

}
