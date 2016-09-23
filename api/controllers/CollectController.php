<?php
namespace api\controllers;
use api\models\GoodCollection;
use api\models\GoodInfo;
use api\models\UserInfo;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Created by PhpStorm.
 * User: szw
 * Date: 2016/8/24
 * Time: 14:21
 */

class CollectController extends  ApiController{

    /**
     *收藏商品api
     */
    public function actionAdd(){
        $user_id = Yii::$app->user->identity->getId();
        //获取商品id
        $good_id = Yii::$app->request->post('good_id');
        if(empty($good_id)){
            return $this->showResult(301,'获取数据异常');
        }
        //判断产品信息是否存在
        $goodInfo = GoodInfo::findOne(['id'=>$good_id,'is_active'=>1]);
        if(empty($goodInfo)){
            return $this->showResult(304,'未获取到产品信息');
        }
        //判断该商品是否已收藏
        $collectedGood = GoodCollection::find()->where(['uid'=>$user_id,'gid'=>$good_id,'status'=>1])->one();
        if(!empty($collectedGood)){
            return $this->showResult(304,'该商品已收藏');
        }
        //存入数据库
        $collection = new GoodCollection();
        $collection->attributes = [
            'uid'=>$user_id,
            'gid'=>$goodInfo->id,
            'add_at'=>time(),
            'status'=>1,
        ];
        if($collection->save()){
            return $this->showResult(200,'收藏成功');
        }else{
            return $this->showResult(400,'系统异常，请重试');
        }
    }

    /**
     *收藏列表
     */
    public function actionCollectionList(){
        $user_id = Yii::$app->user->identity->getId();
        //页数；
        $page = Yii::$app->request->post('page',1);
        $pageSize = Yii::$app->params['pageSize'];
        $userInfo = UserInfo::findOne($user_id);
        if(empty($userInfo)){
            return $this->showResult(302,'用户信息异常');
        }
        //找出收藏数据    good_collection 与 good_info
        $query = GoodCollection::find()->joinWith('g')->where(['status'=>1,'uid'=>$user_id])->orderBy(['add_at'=>SORT_DESC]);
        $count = $query->count();
        $query->offset(($page-1)*$pageSize)->limit($pageSize);
        $collections = $query->all();
        //对数据进行处理并返回
        $data = [];
        foreach($collections as $collection){
            $is_rush = empty($collection->g->goodRush) ? 0:1;
            $is_vip = !empty($collection->g->goodVip)&&$userInfo->is_vip == 1 ? 1:0;
            if($is_rush == 1){
                $salePrice = $collection->g->goodRush->price;
            }elseif($is_vip == 1){
                $salePrice = $collection->g->goodVip->price;
            }else{
                $salePrice = $collection->g->price;
            }
            $data[] = [
                'collection_id'=>$collection->id,
                'good_id'=>$collection->g->id,
                'pic'=>Yii::$app->params['img_path'].$collection->g->pic,
                'name'=>$collection->g->name,
                'volum'=>$collection->g->volum,
                'number'=>$collection->g->number,
                'sale_price'=>$salePrice,
                'end_at' => $is_rush==1 ? $collection->g->goodRush->end_at : '',
                'original_price'=>$collection->g->price,
                'unit'=>$collection->g->unit,
                'is_rush'=>$is_rush,
                'is_vip'=>$is_vip,
            ];
        }
        return $this->showList(200,'收藏列表如下',$count,$data);
    }

    /**
     * 删除收藏
     */
    public function actionDel(){
        $user_id = Yii::$app->user->identity->getId();
        $collection_id = json_decode(stripcslashes(Yii::$app->request->post('collection_id')),true);
        if(empty($collection_id)){
            return $this->showResult(301,'获取数据异常');
        }
        $collection_id = '('.implode(',',$collection_id).')';
        $isExist = GoodCollection::find()->where("id in $collection_id and status=1 and uid=$user_id")->all();
        if(empty($isExist)){
            return $this->showResult(304,'未获取到收藏信息');
        }
        $sql = "DELETE FROM good_collection WHERE id IN $collection_id AND uid=$user_id AND status=1";
        $row = Yii::$app->db->createCommand($sql)->execute();
        if(empty($row)){
            return $this->showResult(400,'系统异常，请重试');
        }else{
            return $this->showResult(200,'删除成功');
        }
    }

}