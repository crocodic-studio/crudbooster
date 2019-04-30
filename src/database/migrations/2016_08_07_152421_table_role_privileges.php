<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class TableRolePrivileges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_role_privileges', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("cb_roles_id");
            $table->integer("cb_menus_id");
            $table->tinyInteger("can_browse")->default(1);
            $table->tinyInteger("can_create")->default(1);
            $table->tinyInteger("can_read")->default(1);
            $table->tinyInteger("can_update")->default(1);
            $table->tinyInteger("can_delete")->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cb_role_privileges');
    }
}
