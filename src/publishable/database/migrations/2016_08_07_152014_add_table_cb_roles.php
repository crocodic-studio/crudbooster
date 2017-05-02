<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCbRoles extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cb_roles', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->nullable();
			$table->boolean('is_superadmin')->nullable();
			$table->string('theme_color')->nullable();
			
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
		Schema::drop('cb_roles');
	}

}
