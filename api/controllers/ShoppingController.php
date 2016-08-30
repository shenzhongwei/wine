<?php
namespace api\controllers;

use api\models\GoodInfo;
use api\models\ShoppingCert;
use api\models\UserInfo;
use common\helpers\ArrayHelper;
use Yii;
use yii\base\Exception;

/**
 * Created by PhpStorm.
 * User: me
 * Date: 2016/8/25
 * Time: 11:52
 * 购物车相关接口
 */

class ShoppingController extends ApiController{


    /**
     * 购物车列表接口
     */
    public function actionShoppingCertList(){
        $user_id = Yii::$app->user->identity->getId();
        //找出购物车内的产品
        $shopCerts = ShoppingCert::find()->joinWith('g')->where(['uid'=>$user_id,'is_active'=>1])->all();
        $userInfo = UserInfo::findOne($user_id);
        if(empty($userInfo)){
            return $this->showResult(304,'未获取到您的用户信息');
        }
        $data = [];
        //队购物车内产品数据处理
        if(!empty($shopCerts)){
            $data = ArrayHelper::getColumn($shopCerts,function($element){
                $user_vip = $element->u->is_vip;
                $good_vip = empty($element->g->goodVip) ? 0:1;
                if($user_vip==1 && $good_vip==1){
                    $price = $element->g->goodVip->price;
                }else{
                    $price = $element->g->price;
                }
                return [
                    'cert_id'=>$element->id,
                    'good_id'=>$element->g->id,
                    'pic'=>Yii::$app->params['img_path'].$element->g->pic,
                    'name'=>$element->g->name,
                    'volum'=>$element->g->volum,
                    'number'=>$element->g->number,
                    'is_active'=>$element->g->is_active,
                    'amount'=>$element->amount,
                    'sale_price'=>$price,
                    'original_price'=>$element->g->price,
                ];
            });
        }
        return $this->showResult(200,'列表如下',$data);
    }

    /**
     * 删除购物车API
     */
    public function actionDel(){
        $user_id = Yii::$app->user->identity->getId();
        //获取数据
        $cert_id = Yii::$app->request->post('cert_id',[]);
        if(empty($cert_id)){
            return $this->showResult(301,'读取购物车信息失败');
        }
        $certIds = '('.implode(',',$cert_id).')';
        //判断是否是该用户的;若不是则返回错误信息
        $userCert = ShoppingCert::find()->where("uid=$user_id and id in $certIds")->all();
        if(empty($userCert)){
            return $this->showResult(304,'数据异常，请重试');
        }
        //进行删除操作
        $sql = "DELETE FROM shopping_cert WHERE uid=$user_id AND id IN $certIds";
        $row = Yii::$app->db->createCommand($sql)->execute();
        if(!empty($row)){
            return $this->showResult(200,'删除成功');
        }else{
            return $this->showResult(400,'删除失败，请重试');
        }
    }

    /**
     * 加入购物车API
     */
    public function actionAdd(){
        $user_id = Yii::$app->user->identity->getId();
        //获取数据,
        $good_id = Yii::$app->request->post('good_id');
        $amount = Yii::$app->request->post('amount');
        //判断是否为空
        if(empty($good_id)||empty($amount)||empty(GoodInfo::findOne($good_id))){
            return $this->showResult(301,'获取商品信息失败');
        }
        //判断购物车中是否有该商品,若是有则直接加上数量，若没有，则新增
        $userCert = ShoppingCert::findOne(['gid'=>$good_id,'uid'=>$user_id]);
        if(empty($userCert)){
            $userCert = new ShoppingCert();
            $userCert->attributes = [
                'uid'=>$user_id,
                'gid'=>$good_id,
                'amount'=>$amount,
            ];
        }else{
            $userCert->amount = $userCert->amount+$amount;
        }
        //保存
        if(!$userCert->save()){
            return $this->showResult(400,'添加失败，请重试');
        }else{
            return $this->showResult(200,'添加成功');
        }
    }

    /**
     * 编辑购物车
     */
    public function actionEdit(){
        $user_id = Yii::$app->user->identity->getId();
        //获取json数据
        $data = json_decode(Yii::$app->request->post('data'),true);
        if(empty($data)){
            return $this->showResult(301,'未获取到详细数据');
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            foreach($data as $value){
                if(empty($value['cert_id']||empty($value['amount']))){
                    throw new Exception('并为读取到产品信息');
                }
                if($value['amount']<=0){
                    throw new Exception('数量超出范围');
                }
                $userCert = ShoppingCert::findOne(['uid'=>$user_id,'id'=>$value['cert_id']]);
                if(empty($userCert)){
                    throw new Exception('数据信息读取失败');
                }
                $userCert->amount = $value['amount'];
                if(!$userCert->save()){
                    throw new Exception('保存购物车信息失败');
                }
            }
            $transaction->commit();
            return $this->showResult(200,'修改成功');
        }catch(Exception $e){
            $transaction ->rollBack();
            return $this->showResult(400,$e->getMessage());
        }
    }


}