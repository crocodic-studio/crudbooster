<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCmsModulsGroup extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_moduls_group', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->string('nama_group');
			$table->string('sorting_group');
			$table->boolean('is_group');
			$table->string('icon_group');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cms_moduls_group');
	}

}
