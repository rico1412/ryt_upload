<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkTimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_time', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bank_code', 32)->default('')->comment('项目代码');
            $table->unsignedInteger('on_duty_time')->default(0)->comment('上班时间');
            $table->unsignedInteger('off_duty_time')->default(0)->comment('下班时间');

            $table->unsignedInteger('add_time')->default(0)->comment('添加时间');
            $table->unsignedInteger('last_update_time')->default(0)->comment('最后更新时间');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_time');
    }
}
