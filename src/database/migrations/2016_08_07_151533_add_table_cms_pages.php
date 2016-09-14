<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCmsPages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_pages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->string('slug')->nullable();
			$table->string('title')->nullable();
			$table->text('content')->nullable();
			$table->softDeletes()->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cms_pages');
	}

}
