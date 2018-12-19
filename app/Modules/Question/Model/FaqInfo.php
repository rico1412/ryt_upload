<?php

namespace App\Modules\Question\Model;

use App\Kernel\Traits\ModelTimeTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 问答信息模型
 *
 * @author 秦昊
 * Date: 2018/8/31 09:18
 */
class FaqInfo extends Model
{
    use SoftDeletes, ModelTimeTraits;

    /**
     * @var string
     */
    protected $table = 'faq_info';

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'copy_total_count',
        'best_answer_id',
        'best_answer_copy_count',
        'best_answer_content',
        'search',
        'add_time',
        'last_update_time',
        'last_edit_time',
    ];

    protected $casts = [
        'title'                     => 'string',
        'description'               => 'string',
        'copy_total_count'          => 'integer',
        'best_answer_id'            => 'integer',
        'best_answer_copy_count'    => 'integer',
        'best_answer_content'       => 'string',
        'search'                    => 'string',
        'add_time'                  => 'integer',
        'last_update_time'          => 'integer',
        'last_edit_time'            => 'integer',
        'created_at'                => 'timestamp',
        'updated_at'                => 'timestamp',
        'deleted_at'                => 'timestamp',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'best_answer_new_content'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * 追加返回格式化后的内容数据
     *
     * @author 秦昊
     * Date: 2018/9/5 15:41
     * @return mixed
     */
    public function getBestAnswerNewContentAttribute()
    {
        return format_content($this->best_answer_content);
    }

    /**
     * 构建搜索字段
     *
     * @author 秦昊
     * Date: 2018/9/3 10:31
     * @param $answerContent
     * @return string
     */
    public function buildSearch($answerContent = null)
    {
        // 冗余 标题+标签+最优答案
        $search = $this->title;
        foreach ($this->Tags->toArray() as $tag) {
            $search .= $tag['name'];
        }
        $search .= $answerContent ?? $this->best_answer_content;

        return $search;
    }

    /**
     * 根据id搜索
     *
     * @author 秦昊
     * Date: 2018/9/4 17:47
     * @param $query
     * @param $id
     * @return mixed
     */
    public function scopeIdQuery($query, $id)
    {
        return $query->where('id', $id);
    }

    /**
     * 根据问答标题模糊搜索
     *
     * @author 秦昊
     * Date: 2018/8/31 10:52
     * @param $query
     * @param $title
     * @return mixed
     */
    public function scopeTitleQuery($query, $title)
    {
        return $query->where('title', 'LIKE', '%'.$title.'%');
    }

    /**
     * 根据关键字模糊搜索
     *
     * @author 秦昊
     * Date: 2018/9/1 19:58
     * @param $query
     * @param $search
     * @return mixed
     */
    public function scopeSearchQuery($query, $search)
    {
        return $query->where('search', 'LIKE', '%'.$search.'%');
    }

    /**
     * 默认排序规则
     *
     * @author 秦昊
     * Date: 2018/9/5 10:54
     * @param $query
     */
    public function scopeDefaultSort($query)
    {
        $query->orderBy('last_edit_time', 'DESC');

        $query->orderBy('id', 'DESC');
    }

    /**
     * 问答信息与问答标签的关联关系
     * 多对多
     *
     * @author 秦昊
     * Date: 2018/9/1 21:46
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function Tags()
    {
        return $this->belongsToMany(
            \App\Modules\Question\Model\FaqTags::class,
            'faq_relation_info_tags',
            'faq_info_id',
            'faq_tag_id'
        );
    }

    /**
     * 问答信息与其答案的关联关系
     * 一对多
     *
     * @author 秦昊
     * Date: 2018/9/1 21:46
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Answers()
    {
        return $this->hasMany(
            \App\Modules\Question\Model\FaqAnswer::class,
            'faq_info_id',
            'id'
        );
    }
}
