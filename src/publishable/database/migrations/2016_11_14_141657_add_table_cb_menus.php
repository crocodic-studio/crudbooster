<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCbMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_menus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('type')->default('url');
            $table->string('path')->nullable();
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->integer('parent_id')->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('is_dashboard')->default(0);
            $table->integer('cb_roles_id')->nullable();
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
        Schema::drop('cb_menus');
    }
}
