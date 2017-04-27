<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNullableCmsApicustom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cms_apicustom', function (Blueprint $table) {
            $table->string('permalink')->nullable()->change();
            $table->string('tabel')->nullable()->change();
            $table->string('aksi')->nullable()->change();
            $table->string('kolom')->nullable()->change();
            $table->string('orderby')->nullable()->change();
            $table->string('sub_query_1')->nullable()->change();
            $table->string('sql_where')->nullable()->change();
            $table->string('nama')->nullable()->change();
            $table->string('keterangan')->nullable()->change();
            $table->string('parameter')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cms_settings', function (Blueprint $table) {
            //
        });
    }
}
