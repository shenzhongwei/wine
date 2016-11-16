<?php

namespace admin\controllers;

use admin\models\OrderInfo;
use admin\models\Push;
use common\JPush\PushModel;
use Yii;
use admin\models\UserInfo;
use admin\models\UserInfoSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * UserController implements the CRUD actions for UserInfo model.
 */
class UserController extends Controller
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
        $searchModel = new UserInfoSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $dataProvider->pagination = [
            'pageSize'=>15,
        ];
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }


    public function actionView($id)
    {
        $model = $this->findModel($id);
        $query = OrderInfo::find()->where(['uid'=>$model->id])->orderBy(['order_date'=>SORT_DESC]);
        $userOrders = new ActiveDataProvider([
            'query'=>$query,
        ]);
        $userOrders->pagination = [
            'pageSize'=>5,
        ];
        return $this->render('view', ['model' => $model,'orders'=>$userOrders]);
    }


    public function actionCreate()
    {
        $model = new UserInfo;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'p1'=>'',
                'PreviewConfig'=>[]
            ]);
        }
    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        // 对用户头像进行处理
        $p1 ='';$P= [];
        if ($model) {
            $p1 = Yii::$app->params['img_path'].$model->head_url;
            $P = [
                'url' =>Url::toRoute('/user/delete-img'),
                'key' => $model->id,
                'width'=>'200px'
            ];
        }

        //上传用户头像
        $img =UploadedFile::getInstance($model,'head_url');
        $pic_path = '../../photo/logo/';
        $img_temp='/logo/';
        $userlogo=SiteController::actionUpload($id,$img,$pic_path,$img_temp);

        if (Yii::$app->request->post()) {
            $userinfo=Yii::$app->request->post('UserInfo');

            $model->attributes=[
                'sex'=>$userinfo['sex'],
                'head_url'=>empty($userlogo)?$userinfo['header']:$userlogo,
                'birth'=>$userinfo['birth'],
                'nickname'=>$userinfo['nickname'],
                'is_vip'=>$userinfo['is_vip'],
                'updated_time'=>date('Y-m-d H:i:s'),
            ];
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->id]);
            }

        } else {
            $usermodel=UserInfo::findOne(['id'=>$model->invite_user_id]);
            $model->invite_user=$usermodel->realname.'('.$usermodel->nickname.')';
            return $this->render('update', [
                'model' => $model,
                'p1'=>$p1,
                'PreviewConfig'=>$P
            ]);
        }
    }


    public function actionDelete($id)
    {
        $user=$this->findModel($id);
        $status = $user->status==0 ? 1:0;
        $user->status = $status;
        $user->updated_time = date('Y-m-d H:i:s');
        $user->save();
        return $this->redirect(['index']);
    }

    public function actionPush(){
        $post = Yii::$app->request->post('Push');
        $content = $post['content'];
        if(!empty($content)){
            $push = new PushModel();
            $res = $push->PushAll($content);
            if($res){
                Yii::$app->session->setFlash('success','推送成功');
            }else{
                Yii::$app->session->setFlash('danger','推送失败');
            }
        }
        return $this->render('push',['model'=>new Push()]);
    }

    public function actionDeleteImg($id)
    {
        $query=$this->findModel($id);
        $query->header_url='';
        $query->save();

    }

    protected function findModel($id)
    {
        if (($model = UserInfo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
