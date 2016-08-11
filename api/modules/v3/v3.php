<?php

namespace v3;
use Yii;
use yii\base\Module;
class V3 extends Module
{
    public function init()
    {
        parent::init();

        // ...  其他初始化代码 ...
        Yii::configure($this, require(__DIR__ . '/config.php'));
    }
}