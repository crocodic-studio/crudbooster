<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCmsMenus extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_menus', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->integer('id_cms_menus_groups')->nullable();
			$table->string('name')->nullable();
			$table->string('menu_type')->nullable();
			$table->string('menu_link')->nullable();
			$table->integer('id_cms_pages')->nullable();
			$table->integer('id_cms_posts')->nullable();
			$table->integer('id_cms_posts_categories')->nullable();
			$table->integer('parent_id_cms_menus')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cms_menus');
	}

}
