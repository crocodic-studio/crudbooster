<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCmsLogsTable extends Migration {

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
			$table->string('ipaddress', 50)->nullable();
			$table->string('useragent')->nullable();
			$table->string('url')->nullable();
			$table->string('description')->nullable();
			$table->integer('cms_users_id')->nullable();
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
		Schema::drop('cms_logs');
	}

}
