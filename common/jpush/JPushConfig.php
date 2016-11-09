<?php
/**
 * Created by PhpStorm.
 * User: WangYong
 * Date: 2015/12/9
 * Time: 13:29
 */
namespace common\jpush;

use Yii;

class JPushConfig{
    const APPKEY = '85251b93cedf8de1f8a0a71a';            //待发送的应用程序(appKey)，只能填一个。
    const MASTERSECRET = 'd22be4231f8cf08ed7e5fa63';//主密码
    const APPKEY_COM = '';
    const MASTERSECRET_COM = '';
    const PUSHURL = "https://api.jpush.cn/v3/push";      //推送的地址
    const VALIDURL ='https://api.jpush.cn/v3/push/validate';//验证地址
}