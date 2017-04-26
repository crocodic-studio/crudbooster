<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddResponsesApicustom extends Migration
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
            $table->longText('responses')->nullable();
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
            $table->dropColumn('responses');
        });
    }
}
