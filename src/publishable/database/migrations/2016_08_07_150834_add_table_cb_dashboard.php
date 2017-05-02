<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCbDashboard extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cb_dashboard', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('cb_roles_id')->nullable();
			$table->string('name')->nullable();			
			$table->longtext('content')->nullable();
			
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
		Schema::drop('cb_dashboard');
	}

}
