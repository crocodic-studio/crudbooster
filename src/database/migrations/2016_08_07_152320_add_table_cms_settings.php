<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCmsSettings extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_settings', function(Blueprint $table)
		{
			$table->increments('id');
			
			$table->string('name')->nullable();
			$table->text('content')->nullable();
			$table->string('content_input_type')->nullable();
			$table->string('dataenum')->nullable();
			$table->string('helper')->nullable();
			
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
		Schema::drop('cms_settings');
	}

}
