<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_menus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30)->nullable();
            $table->string('type', 30)->default('url');
            $table->string('path', 50)->nullable();
            $table->string('color', 30)->nullable();
            $table->string('icon', 30)->nullable();
            $table->integer('parent_id')->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('is_dashboard')->default(0);
            $table->integer('id_cms_privileges')->nullable();
            $table->integer('sorting')->nullable();

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
        Schema::drop('cms_menus');
    }
}
