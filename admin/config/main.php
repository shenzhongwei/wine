<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
return [
    "modules" => [
        'redactor' => 'yii\redactor\RedactorModule',
        "admin" => [
            "class" => 'mdm\admin\Module',
        ],
        'gridview' =>  [
            'class' => '\kartik\grid\Module',
            'downloadAction' => 'gridview/export/download',
            'datecontrol' =>  [
                'class' => 'kartik\datecontrol\Module',
                // format settings for displaying each date attribute
                'displaySettings' => [
                    'date' => 'd-m-Y',
                    'time' => 'H:i:s A',
                    'datetime' => 'd-m-Y H:i:s A',
                ],

                // format settings for saving each date attribute
                'saveSettings' => [
                    'date' => 'Y-m-d',
                    'time' => 'H:i:s',
                    'datetime' => 'Y-m-d H:i:s',
                ],



                // automatically use kartik\widgets for each of the above formats
                'autoWidget' => true,

            ]
        ],
    ],
    "aliases" => [
        "@mdm/admin" => "@vendor/mdmsoft/yii2-admin",
    ],

    'as access' => [
        //ACF肯定要加,加了才会自动验证是否有权限
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            'debug/*',
            'site/login',
            'site/error',
        ],
    ],

    'id' => 'wine-manage',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'admin\controllers',
    'bootstrap' => ['log'],
    'language'=>'zh-CN',
    'components' => [
        'request' => [
            'csrfParam' => '_wine-admin',
        ],
        'user' => [
            'identityClass' => 'admin\models\Admin',
            'loginUrl'=>['site/login'],
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_wine-admin', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'wine-admin',
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

        "urlManager" => [
            //用于表明urlManager是否启用URL美化功能，在Yii1.1中称为path格式URL，
            // Yii2.0中改称美化。
            // 默认不启用。但实际使用中，特别是产品环境，一般都会启用。
            "enablePrettyUrl" => true,
            // 是否启用严格解析，如启用严格解析，要求当前请求应至少匹配1个路由规则，
            // 否则认为是无效路由。
            // 这个选项仅在 enablePrettyUrl 启用后才有效。
            "enableStrictParsing" => false,
            // 是否在URL中显示入口脚本。是对美化功能的进一步补充。
            "showScriptName" => false,
            // 指定续接在URL后面的一个后缀，如 .html 之类的。仅在 enablePrettyUrl 启用时有效。
            "suffix" => "",
            "rules" => [
                "<controller:\w+>/<id:\d+>"=>"<controller>/view",
                "<controller:\w+>/<action:\w+>"=>"<controller>/<action>"
            ],
        ],

        'authManager' => [
            'class' => 'yii\rbac\DbManager', // 使用数据库管理配置文件
            "defaultRoles" => ["guest"],
        ],
        'assetManager'=>[
            'bundles'=>[
                'yii\web\JqueryAsset' => [
                    'js'=>[]
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js'=>[]
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [],
                ],
            ],

        ],
    ],
    'params' => $params,
];

