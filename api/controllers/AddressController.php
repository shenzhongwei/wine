<?php
namespace api\controllers;

use api\models\UserAddress;
use Yii;
use yii\base\Exception;

/**
 * Created by PhpStorm.
 * User: me
 * Date: 2016/8/12
 * Time: 16:50
 */
class AddressController extends ApiController{

    /**
     * 增加地址接口
     */
    public function actionAdd(){
        $user = Yii::$app->user->identity;
        //获取参数
        $receiver = Yii::$app->request->post('receiver');
        $phone = Yii::$app->request->post('phone');
        $province = Yii::$app->request->post('province');
        $city = Yii::$app->request->post('city');
        $district = Yii::$app->request->post('district');
        $street = Yii::$app->request->post('street');
        $address = Yii::$app->request->post('address');
        $lat = Yii::$app->request->post('lat');
        $lng = Yii::$app->request->post('lng');
        $tag = Yii::$app->request->post('tag','');
        /**
         * 判断非空
         */
        if(empty($province)||empty($city)||empty($district)){
            return $this->showResult(301,'请选择省市区');
        }
        if(empty($phone)||empty($receiver)){
            return $this->showResult(301,'未获取到收货人信息');
        }
        if(!$this->validateMobilePhone($phone)){
            return $this->showResult(301,'手机格式错误');
        }
        if(empty($street)||empty($address)){
            return $this->showResult(301,'未获取到收货地址信息');
        }
        if(empty($lng)||empty($lat)){
            return $this->showResult(301,'未获取到收货地址信息');
        }
        //插入数据库
        $userAddress = new UserAddress();
        $userAddress->attributes = [
            'uid'=>$user->id,
            'get_phone'=>$phone,
            'get_person'=>$receiver,
            'province'=>$province,
            'city'=>$city,
            'district'=>$district,
            'region'=>$street,
            'address'=>$address,
            'lat'=>(int)($lat*1000000),
            'lng'=>(int)($lng*1000000),
            'tag'=>$tag,
            'created_time'=>date('Y-m-d H:i:s'),
            'updated_time'=>date('Y-m-d H:i:s'),
            'status'=>1,
        ];
        if(!$userAddress->save()){
            return $this->showResult(400,'新增地址信息出错');
        }else{
            return $this->showResult(200,'新增地址成功');
        }
    }

    /**
     * 设置默认地址api
     */
    public function actionSetDefault(){
        $user_id = Yii::$app->user->identity->getId();
        $address_id = Yii::$app->request->post('address_id','');
        //判断餐时是否传递
        if(empty($address_id)){
            return $this->showResult(301,'未获取到您的地址信息');
        }
        //判断地址是否为该用户的
        $userAddress = UserAddress::findOne(['id'=>$address_id,'uid'=>$user_id]);
        if(empty($userAddress)){
            return $this->showResult(304,'该地址数据异常，请稍后重试');
        }
        //将其他地址改为非默认
        $otherAddress =  UserAddress::find()->where("id<>$address_id and uid=$user_id and status<>0")->all();
        $transaction = Yii::$app->db->beginTransaction();
        try{
            //修改为默认地址
            $userAddress->is_default = 1;
            $userAddress->status = 1;
            $userAddress->updated_time = date('Y-m-d H:i:s');
            if(!$userAddress->save()){
                throw new Exception('修改地址状态出错');
            }
            if(!empty($otherAddress)){
                $sql = "UPDATE mm_user_address SET is_default=0 WHERE id<>$address_id AND uid=$user_id AND status<>0";
                $row = Yii::$app->db->createCommand($sql)->execute();
                if(empty($row)){
                    throw new Exception('修改其他地址状态出错');
                }
            }
        }catch (Exception $e){
            $transaction->rollBack();
            return $this->showResult(400,$e->getMessage());
        }
    }

    /**
     * 删除地址api
     */
    public function actionDel(){
        $user_id = Yii::$app->user->identity->getId();
        $address_id = Yii::$app->request->post('address_id','');
        //判断餐时是否传递
        if(empty($address_id)){
            return $this->showResult(301,'未获取到您的地址信息');
        }
        //判断地址是否为该用户的
        $userAddress = UserAddress::findOne(['id'=>$address_id,'uid'=>$user_id]);
        if(empty($userAddress)){
            return $this->showResult(304,'该地址数据异常，请稍后重试');
        }
        $userAddress->status = 0;
        if(!$userAddress->save()){
            return $this->showResult(400,'删除地址失败');
        }else{
            return $this->showResult(200,'删除地址成功');
        }
    }

    /**
     * 地址列表api
     */
    public function actionAddList(){
        //获取用户id
        $user_id = Yii::$app->user->identity->getId();
        $adds = UserAddress::find()->where(['and','uid='.$user_id,'status<>0','lat>0','lng>0'])->all();
        //判断是否有地址
        if(empty($adds)){
            return $this->showResult(303,'尚未添加收货地址');
        }else{
            //写入数组返回
            $data = [];
            foreach($adds as $add){
                $data[] = [
                    'address_id'=>$add->id,
                    'receiver'=>$add->get_person,
                    'phone'=>$add->get_phone,
                    'address'=>$add->province.$add->city.$add->district.$add->region.$add->address,
                    'lat'=>$add->lat/1000000,
                    'lng'=>$add->lng/1000000,
                    'tag'=>$add->tag,
                    'default'=>$add->is_default,
                ];
            }
            return $this->showResult(200,'获取成功',$data);
        }
    }

    /**
     * 编辑地址api
     */
    public function actionEdit(){
        $user_id = Yii::$app->user->identity->getId();
        $address_id = Yii::$app->request->post('address_id','');
        //判断id是否传递
        if(empty($address_id)){
            return $this->showResult(301,'未获取到您的地址信息');
        }
        //判断地址是否为该用户的
        $userAddress = UserAddress::findOne(['id'=>$address_id,'uid'=>$user_id]);
        if(empty($userAddress)){
            return $this->showResult(304,'该地址数据异常，请稍后重试');
        }
        //获取参数
        $receiver = Yii::$app->request->post('receiver');
        $phone = Yii::$app->request->post('phone');
        $province = Yii::$app->request->post('province');
        $city = Yii::$app->request->post('city');
        $district = Yii::$app->request->post('district');
        $street = Yii::$app->request->post('street');
        $address = Yii::$app->request->post('address');
        $lat = Yii::$app->request->post('lat');
        $lng = Yii::$app->request->post('lng');
        $tag = Yii::$app->request->post('tag','');
        /**
         * 判断非空
         */
        if(empty($province)||empty($city)||empty($district)){
            return $this->showResult(301,'请选择省市区');
        }
        if(empty($phone)||empty($receiver)){
            return $this->showResult(301,'未获取到收货人信息');
        }
        if(!$this->validateMobilePhone($phone)){
            return $this->showResult(301,'手机格式错误');
        }
        if(empty($street)||empty($address)){
            return $this->showResult(301,'未获取到收货地址信息');
        }
        if(empty($lng)||empty($lat)){
            return $this->showResult(301,'未获取到收货地址信息');
        }
        //保存数据
        $userAddress->attributes = [
            'get_phone'=>$phone,
            'get_person'=>$receiver,
            'province'=>$province,
            'city'=>$city,
            'district'=>$district,
            'region'=>$street,
            'address'=>$address,
            'lat'=>(int)($lat*1000000),
            'lng'=>(int)($lng*1000000),
            'tag'=>$tag,
            'status'=>1,
            'updated_time'=>date('Y-m-d H:i:s'),
        ];
        if(!$userAddress->save()){
            return $this->showResult(400,'新增地址信息出错');
        }else{
            return $this->showResult(200,'新增地址成功');
        }
    }
}