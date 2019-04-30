<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class TableMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_menus', function (Blueprint $table) {
            $table->increments("id");
            $table->string('name');
            $table->string("icon")->nullable();
            $table->string("path")->nullable();
            $table->string("type");
            $table->integer("sort_number")->default(0);
            $table->integer("cb_modules_id")->nullable();
            $table->integer("parent_cb_menus_id")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cb_menus');
    }
}
