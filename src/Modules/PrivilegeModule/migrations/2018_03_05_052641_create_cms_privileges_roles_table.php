<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCmsPrivilegesRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_roles_privileges', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean('can_see_module')->nullable();
			$table->boolean('can_create')->nullable();
			$table->boolean('can_read')->nullable();
			$table->boolean('can_edit')->nullable();
			$table->boolean('can_delete')->nullable();
			$table->integer('cms_roles_id')->nullable();
			$table->integer('cms_modules_id')->nullable();
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
		Schema::drop('cms_roles_privileges');
	}

}
