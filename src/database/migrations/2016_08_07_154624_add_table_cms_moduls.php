<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCmsModuls extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_moduls', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->string('name')->nullable();
			$table->string('icon')->nullable();
			$table->string('path')->nullable();
			$table->string('table_name')->nullable();
			$table->string('controller')->nullable();
			$table->string('sql_where')->nullable();
			$table->string('sql_orderby')->nullable();
			$table->integer('sorting')->nullable();
			$table->integer('limit_data')->nullable();
			$table->integer('id_cms_moduls_group')->nullable();
			$table->boolean('is_softdelete')->nullable();
			$table->boolean('is_active')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cms_moduls');
	}

}
