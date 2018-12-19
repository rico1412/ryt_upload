<?php

namespace App\Validator;


class CharacterLength
{

    /**
     * 校验字符长度
     * User: lizhenhai
     * Date: 2018/7/24 0024
     * @param $attr
     * @param $value
     * @param $max
     * @return bool
     */
    public function length($attr, $value, $max)
    {
        if ($value) {
            //中文匹配
            preg_match_all('/[\x7f-\xff]/', $value, $ch);

            //中文的文字个数（utf-8中文占三个字符）
            $chLength = count($ch[0]) / 3;

            //mb_strlen用utf-8编码所有字符的长度都是1，这里计算出总长度
            $valueLength = mb_strlen($value, 'utf-8');

            //需求一个中文占两个字符，总长度减去中文文字个数再加上中文字符长度不能超过最大限制值
            return $valueLength - $chLength + $chLength * 2 <= $max[0];
        }

        return true;
    }
}
