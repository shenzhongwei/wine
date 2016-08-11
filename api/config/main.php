<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'shante-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'components' => [
        #支付宝支付
        'alipay' => [
            'class'=>'common\pay\alipay\AlipayNotify',
        ],
        'jpush' => [
            'class' => 'common\jpush\JPush',
        ],
        'cache'=> [
            'class' => 'yii\caching\FileCache',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            //'cookieValidationKey' => 'nPY8jIj6BgRnL2W7iowq-vJBR_y1VJ-4',
            'enableCookieValidation'=>false,
            'enableCsrfValidation'=>false
        ],
        'response'=>[
            'class'=>'yii\web\Response',
            'format'=>\yii\web\Response::FORMAT_JSON,
        ],
//        'user'=>[
//            'identityClass'=>'api\models\UserLogin',
//            'enableSession'=>false,
//            'loginUrl'=>null,
//        ],
        "urlManager" => [
            //用于表明urlManager是否启用URL美化功能，在Yii1.1中称为path格式URL，
            // Yii2.0中改称美化。
            // 默认不启用。但实际使用中，特别是产品环境，一般都会启用。
            "enablePrettyUrl" => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
];
