<?php
namespace common\JPush;

use Yii;
/**
 * Created by PhpStorm.
 * User: 11633
 * Date: 2016-11-16
 * Time: 14:20
 */
class PushModel{

    public function PushReg($content,$registArr,$title='',$extra=[],$alert='',$env=false,$alias=[],$tag=[],$platform=['ios', 'android']){
        $key = Yii::$app->params['pushParams']['key'];
        $secret = Yii::$app->params['pushParams']['secret'];
        $push = new Jpush($key,$secret);
        // 完整的推送示例,包含指定Platform,指定Alias,Tag,指定iOS,Android notification,指定Message等
        $result = $push->push()
            ->setPlatform($platform)
            ->addRegistrationId($registArr)
            ->setNotificationAlert($alert)
            ->addAndroidNotification($content, $title, 1, $extra)
            ->addIosNotification($content,  Config::DISABLE_SOUND, Config::DISABLE_BADGE, true,null, $extra)
            ->setMessage($content, $title, null, $extra)
            ->setOptions(100000, 3600, null, $env,null)
            ->send();
        if($result->status==200){
            return true;
        }else{
            return false;
        }
    }


    public function PushAll($content,$title='',$extra=[],$alert='',$env=false,$alias=[],$tag=[],$platform=['ios', 'android']){
        $key = Yii::$app->params['pushParams']['key'];
        $secret = Yii::$app->params['pushParams']['secret'];
        $push = new Jpush($key,$secret);
        // 完整的推送示例,包含指定Platform,指定Alias,Tag,指定iOS,Android notification,指定Message等
        $result = $push->push()
            ->setPlatform($platform)
            ->addAllAudience()
            ->setNotificationAlert($alert)
            ->addAndroidNotification($content, $title, 1, $extra)
            ->addIosNotification($content,  Config::DISABLE_SOUND, Config::DISABLE_BADGE, true,null, $extra)
            ->setMessage($content, $title, null, $extra)
            ->setOptions(100000, 3600, null, $env)
            ->send();
        return json_decode($result);
    }
}