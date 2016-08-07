<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCmsDashboard extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_dashboard', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->string('name');
			$table->integer('id_cms_privileges');
			$table->longtext('content');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cms_dashboard');
	}

}
