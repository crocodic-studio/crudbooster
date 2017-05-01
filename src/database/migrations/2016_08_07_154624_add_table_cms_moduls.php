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

			$table->string('name')->nullable();
			$table->string('icon')->nullable();
			$table->string('path')->nullable();
			$table->string('table_name')->nullable();
			$table->string('controller')->nullable();
			$table->boolean('is_protected')->default(0);
			$table->boolean('is_active')->default(0);
			
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
		Schema::drop('cms_moduls');
	}

}
