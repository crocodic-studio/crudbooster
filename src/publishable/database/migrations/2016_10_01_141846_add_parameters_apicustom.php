<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParametersApicustom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cms_apicustom', function (Blueprint $table) {
            //
            $table->longText('parameters')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cms_apicustom', function (Blueprint $table) {
            //
            $table->dropColumn('parameters');
        });
    }
}
