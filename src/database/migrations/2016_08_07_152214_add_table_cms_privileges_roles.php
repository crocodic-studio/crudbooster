<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCmsPrivilegesRoles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_privileges_roles', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->boolean('is_visible');
			$table->boolean('is_create');
			$table->boolean('is_read');
			$table->boolean('is_edit');
			$table->boolean('is_delete');
			$table->integer('id_cms_privileges');
			$table->integer('id_cms_moduls');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cms_privileges_roles');
	}

}
