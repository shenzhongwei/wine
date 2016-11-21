<?php
namespace admin\controllers;

use admin\models\AccountInoutSearch;
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
     * 订单报表列表
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

    /**
     * 充值报表列表
     */
    public function actionAccount()
    {
        $sql = "SELECT SUM(end) AS sum FROM user_account WHERE is_active = 1";
        $total = Yii::$app->db->createCommand($sql)->queryOne();
        $searchModel = new AccountInoutSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        $dataProvider->pagination = false;
        return $this->render('account',[
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'total'=>$total,
        ]);
    }
}

