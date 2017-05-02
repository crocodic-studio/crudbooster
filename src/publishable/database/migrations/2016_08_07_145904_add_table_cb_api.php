<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCbApi extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cb_api', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->nullable();
			$table->string('description')->nullable();
			$table->longText('params')->nullable();
			$table->string('permalink')->nullable();
			$table->string('table')->nullable();
			$table->string('action_type')->nullable();
			$table->string('method_type')->nullable();
			$table->longText('responses')->nullable();
			$table->text('columns')->nullable();
			$table->string('order_by')->nullable();
			$table->text('sub_query')->nullable();
			$table->text('sql_where')->nullable();			
			
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
		Schema::drop('cb_api');
	}

}
