<?php
namespace admin\controllers;

use admin\models\OrderInfoSearch;
use admin\models\ReportSearch;
use Yii;
/**
 * Created by PhpStorm.
 * User: 11633
 * Date: 2016/10/14
 * Time: 16:20
 */

class ReportController extends BaseController {

    /**
     * 报表列表
     */
    public function actionIndex()
    {
        $searchModel = new ReportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        return $this->render('index',[
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
}

