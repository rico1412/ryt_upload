<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastEditTimeColumnToFaqInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('faq_info', function (Blueprint $table) {
            $table->unsignedInteger('last_edit_time')->default(0)->comment('最后修改时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('faq_info', function (Blueprint $table) {
            $table->dropColumn('last_edit_time');
        });
    }
}
