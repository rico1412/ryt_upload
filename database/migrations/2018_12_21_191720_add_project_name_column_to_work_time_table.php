<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProjectNameColumnToWorkTimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_time', function (Blueprint $table) {
            $table->string('project_name', 64)->default('')->after('bank_code')->comment('项目名称');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_time', function (Blueprint $table) {
            $table->dropColumn('project_name');
        });
    }
}
