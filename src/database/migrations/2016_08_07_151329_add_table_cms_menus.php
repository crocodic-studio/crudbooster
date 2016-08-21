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
			$table->integer('id_cms_menus_groups');
			$table->string('name');
			$table->string('menu_type');
			$table->string('menu_link');
			$table->integer('id_cms_pages');
			$table->integer('id_cms_posts');
			$table->integer('id_cms_posts_categories');
			$table->integer('parent_id_cms_menus');
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
