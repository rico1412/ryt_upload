<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaqInfoTable extends Migration
{
    protected $tableName = 'faq_info';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100)->default('')->comment('问答标题');
            $table->string('description', 200)->default('')->comment('问答描述');
            $table->integer('copy_total_count')->default(0)->comment('统计所有答案的复制次数');
            $table->unsignedInteger('best_answer_id')->default(0)->comment('最佳答案id');
            $table->unsignedInteger('best_answer_copy_count')->default(0)->comment('最佳答案被复制次数');
            $table->text('best_answer_content')->default('')->comment('最优答案内容');
            $table->text('search')->default('')->comment('冗余【标题+标签+最优答案】');
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
