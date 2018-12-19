<?php

namespace App\Kernel\Traits;

/**
 * 模型时间戳
 *
 * @author 51004
 */
trait ModelTimeTraits
{
    /**
     * Set the value of the "updated at" attribute.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setUpdatedAt($value)
    {
        parent::setUpdatedAt($value);

        $this->last_update_time = $value->timestamp;

        return $this;
    }

    /**
     * Set the value of the "updated at" attribute.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setCreatedAt($value)
    {
        parent::setCreatedAt($value);

        $this->add_time = $value->timestamp;

        return $this;
    }
}