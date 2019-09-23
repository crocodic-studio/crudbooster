<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ModifyUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('photo')->nullable();
            $table->integer('cb_roles_id');
            $table->ipAddress("ip_address")->nullable();
            $table->string("user_agent")->nullable();
            $table->timestamp("login_at")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(["photo","cb_roles_id","ip_address","user_agent","login_at"]);
        });
    }
}
