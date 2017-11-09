<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCmsPrivileges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_privileges', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 50)->nullable();
            $table->boolean('is_superadmin')->nullable();
            $table->string('theme_color', 50)->nullable();

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
        Schema::drop('cms_privileges');
    }
}
