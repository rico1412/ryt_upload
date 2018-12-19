<?php

namespace App\Modules\Question\Invoke;

use App\Kernel\Base\BaseInvoke;
use App\Modules\Question\Business\QuestionBusiness;

/**
 *
 *
 * @author 秦昊
 * Date: 2018/9/10 18:17
 */
class QuestionInvoke extends BaseInvoke
{
    protected $questionBusiness = null;

    public function __construct(QuestionBusiness $questionBusiness)
    {
        $this->questionBusiness = $questionBusiness;
    }

    /**
     *
     *
     * @author 秦昊
     * Date: 2018/9/10 18:20
     * @param $faqInfo
     * @throws \App\Exceptions\FaqInfoException
     */
    public function syncData($faqInfo)
    {
        $this->questionBusiness->syncData($faqInfo->id);
    }

}