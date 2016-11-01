<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCmsApicustom extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms_apicustom', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->string('permalink')->nullable();
			$table->string('tabel')->nullable();
			$table->string('aksi')->nullable();
			$table->text('kolom')->nullable();
			$table->string('orderby')->nullable();
			$table->text('sub_query_1')->nullable();
			$table->text('sql_where')->nullable();
			$table->string('nama')->nullable();
			$table->string('keterangan')->nullable();
			$table->string('parameter')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cms_apicustom');
	}

}
