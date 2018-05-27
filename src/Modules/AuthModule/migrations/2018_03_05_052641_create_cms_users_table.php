<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCmsUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 50)->nullable();
			$table->string('photo')->nullable();
			$table->string('email', 50)->nullable();
			$table->string('password')->nullable();
			$table->integer('cms_privileges_id')->nullable();
			$table->timestamps();
			$table->string('status', 50)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cms_users');
	}

}
