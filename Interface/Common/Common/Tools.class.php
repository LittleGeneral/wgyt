<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-10-10
 * Time: 下午2:42
 */

namespace Common\Common;


class Tools
{
    /**
     * 检查敏感词
     * @param $str 需要检查的非空字符串
     * @return boolean 如果有敏感词则返回false，否则返回true
     */
    public static function CheckChar($str='')
    {
        if(empty($str)) return false;
        $ccs = @file_get_contents('check_char.txt');
        $arr = explode(',',strtolower($ccs));
        $str = strtolower($str);
        foreach($arr as $k=>$v)
        {
            if(strpos($str, $v) !== false) return false;
        }
        return true;
    }
}