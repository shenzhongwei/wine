<?php

namespace admin\controllers;
use admin\models\Log;
use admin\models\Menu;
use admin\models\PasswordForm;
use yii\data\Pagination;

use Yii;

class IndexController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        return $this->render('index');
    }


    public function actionWelcome()
    {
        //$action = Yii::$app->controller->module->requestedRoute;
        //var_dump(\Yii::$app->user->can('/site/index'));exit;
        //最近登录记录
        return $this->render('welcome');
    }
}

