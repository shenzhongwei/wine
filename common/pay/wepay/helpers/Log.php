<?php
/**
 * Created by PhpStorm.
 * User: szw
 * Date: 2015/12/3
 * Time: 15:29
 */

namespace common\pay\wepay\helpers;
/**
 * 将错误写入log文件中的类
 */
class Log{
    // 打印log
    function  log_result($word)
    {
        $fp = fopen("wx_notify.txt","a");
        flock($fp, LOCK_EX) ;
        fwrite($fp,"执行日期：".strftime("%Y-%m-%d-%H：%M：%S",time())."\n".$word."\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}