<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCmsMenusPrivileges extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_menus_privileges', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('id_cms_menus')->nullable();
			$table->integer('id_cms_privileges')->nullable();						
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cms_menus_privileges');
	}

}
