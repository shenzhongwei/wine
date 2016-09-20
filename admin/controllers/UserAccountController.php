<?php

namespace admin\controllers;

use admin\models\AccountInout;
use admin\models\InoutPay;
use Yii;
use admin\models\UserAccount;
use admin\models\UserAccountSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
/**
 * UserAccountController implements the CRUD actions for UserAccount model.
 */
class UserAccountController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post','get'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new UserAccountSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $dataProvider->pagination = [
            'pageSize'=>15,
        ];
        $dataProvider->sort = [
            'defaultOrder'=>['id'=>SORT_ASC,'end'=>SORT_DESC,'start'=>SORT_DESC,]
        ];

        /*********************在gridview列表页面上直接修改数据 start*****************************************/
        //获取前面一部传过来的值
        if (Yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey'); //获取需要编辑的id
            $model = $this->findModel($id);
            $out = Json::encode(['output'=>'', 'message'=>'']);
            //获取输入的金额
            $posted = current($_POST['UserAccount']); //输出数组中当前元素的值，默认初始指向插入到数组中的第一个元素。移动数组内部指针，使用next()和prev()

            $post = ['UserAccount' => $posted];
            $output = '';
            if ($model->load($post)) { //赋值
                if(isset($posted['end'])){
                    $model->end=$posted['end'];
                }
                if(isset($posted['start'])){
                    $model->start=$posted['start'];
                }
                $model->save(); //save()方法会先调用validate()再执行insert()或者update()
                isset($posted['end']) && $output= $model->end; //最终余额
                isset($posted['start']) && $output= $model->start; //初始金额
            }
            $out = Json::encode(['output'=>$output, 'message'=>'']);
            echo $out;
            return;
        }
        /*******************在gridview列表页面上直接修改数据 end***********************************************/


        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /*
     * 账户明细
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $query = AccountInout::find()->where(['aid'=>$id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination = [
            'pageSize'=>10,
        ];

        return $this->render('view', ['model' => $model,'dataProvider'=>$dataProvider]);

    }

    /**
     * Creates a new UserAccount model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserAccount;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }


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
    /*
     * 删除账户
     */
    public function actionDelete($id)
    {
        $model=$this->findModel($id);
        $model->is_active=0;
        $model->save();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = UserAccount::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}
