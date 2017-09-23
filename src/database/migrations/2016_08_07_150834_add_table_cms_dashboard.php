<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddTableCmsDashboard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_dashboard', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->nullable();
            $table->integer('id_cms_privileges')->nullable();
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
        Schema::drop('cms_dashboard');
    }
}
