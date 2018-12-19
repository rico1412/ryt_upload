<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaqAnswerTable extends Migration
{
    protected $tableName = 'faq_answer';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('faq_info_id')->default(0)->comment('当前答案所属问答信息的id')->index();
            $table->text('content')->default('')->comment('答案内容');
            $table->unsignedInteger('copy_count')->default(0)->comment('当前答案被复制的次数');
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
        Schema::dropIfExists($this->tableName);
    }
}
