<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsNewTabToCmsMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cms_menus', function (Blueprint $table) {
			$table->boolean('is_external_link')->default(0)->after('sorting');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cms_menus', function (Blueprint $table) {
            $table->dropColumn('is_external_link');
        });
    }
}
