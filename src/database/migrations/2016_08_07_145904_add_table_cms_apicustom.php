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
			$table->string('permalink');
			$table->string('tabel');
			$table->string('aksi');
			$table->text('kolom');
			$table->string('orderby');
			$table->text('sub_query_1');
			$table->text('sql_where');
			$table->string('nama');
			$table->string('keterangan');
			$table->string('parameter');
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
