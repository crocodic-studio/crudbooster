<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCbLogs extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cb_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('users_id')->nullable();
			$table->string('ipaddress',50)->nullable();
			$table->string('useragent')->nullable();
			$table->string('url')->nullable();
			$table->string('description')->nullable();			
			
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
