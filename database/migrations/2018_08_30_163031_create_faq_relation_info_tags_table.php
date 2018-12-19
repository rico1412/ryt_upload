<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaqRelationInfoTagsTable extends Migration
{
    protected $tableName = 'faq_relation_info_tags';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('faq_info_id')->default(0)->comment('问答信息关联id')->index();
            $table->unsignedInteger('faq_tag_id')->default(0)->comment('问答标签关联id')->index();
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
