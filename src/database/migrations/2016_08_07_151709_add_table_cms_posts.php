<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCmsPosts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_posts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->string('title')->nullable();
			$table->string('slug')->nullable();
			$table->text('content')->nullable();
			$table->integer('id_cms_users')->nullable();
			$table->integer('id_cms_posts_categories')->nullable();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cms_posts');
	}

}
