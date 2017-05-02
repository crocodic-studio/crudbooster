<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCbSettings extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cb_settings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('label')->nullable();
			$table->string('name')->nullable();
			$table->text('content')->nullable();
			$table->string('content_input_type')->nullable();
			$table->string('dataenum')->nullable();
			$table->string('group')->nullable();
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
		Schema::drop('cb_settings');
	}

}
