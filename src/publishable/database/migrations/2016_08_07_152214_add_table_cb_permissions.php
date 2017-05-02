<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCbPermissions extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cb_permissions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean('can_visible')->nullable();
			$table->boolean('can_create')->nullable();
			$table->boolean('can_read')->nullable();
			$table->boolean('can_edit')->nullable();
			$table->boolean('can_delete')->nullable();
			$table->integer('cb_roles_id')->nullable();
			$table->integer('cb_modules_id')->nullable();
			
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
		Schema::drop('cb_permissions');
	}

}
