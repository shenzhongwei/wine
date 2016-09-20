<?php

namespace admin\controllers;

use Yii;
use admin\models\MessageList;
use admin\models\MessageSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MessageController implements the CRUD actions for MessageList model.
 */
class MessageController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all MessageList models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MessageSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $dataProvider->pagination = [
            'pageSize' => 15,
        ];
        $dataProvider->sort = [
            'defaultOrder' => ['publish_at'=>SORT_DESC,'id'=>SORT_DESC]
        ];

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single MessageList model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('view', ['model' => $model]);
        }
    }

    /**
     * Creates a new MessageList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $admin = Yii::$app->user->identity;
        $model = new MessageList;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {

            return $this->render('create', [
                'model' => $model,
                'wa_type'=>$admin->wa_type
            ]);
        }
    }

    /**
     * Updates an existing MessageList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing MessageList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the MessageList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MessageList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MessageList::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
    * 根据相应类型查找对应的名称
    */
    public function actionRelationName(){
        $depDrop = Yii::$app->request->post('depdrop_parents');
        $results = [];
        if (isset($depDrop)) {
            $id = end($depDrop); //输出数组中最后一个元素
            switch($id){
                case 1:
                    $model=array(
                        array(
                            "id"=>"2",
                            "name"=>"系统管理员",
                        )
                    );
                    break;
                case 2: //用户消息
                    $model=\admin\models\UserInfo::find()->select(['id','phone as name'])->asArray()->all();
                    break;
                case 3: //订单消息
                    $model=\admin\models\OrderInfo::find()->select(['id','order_code as name'])->asArray()->all();;
                    break;
                case 4: //商品通知
                    $model=\admin\models\GoodInfo::find()->select(['id','name'])->asArray()->all();;
                    break;
                default:
                    $model=[[]];
                    break;
            }

            if(!empty($model)){
                $results = ArrayHelper::getColumn($model,function($element){
                    return [
                        'id'=>$element['id'],
                        'name'=>$element['name'],
                    ];
                });
            }
        }
        echo Json::encode(['output' => empty($results) ? '':$results, 'selected'=>'']);
        exit;
    }


}
