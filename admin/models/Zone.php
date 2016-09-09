<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "zone".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parentid
 * @property string $shortname
 * @property integer $leveltype
 * @property string $citycode
 * @property string $zipcode
 * @property string $lng
 * @property string $lat
 * @property string $pinyin
 * @property string $status
 */
class Zone extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zone';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'parentid', 'leveltype'], 'integer'],
            [['status'], 'string'],
            [['name', 'shortname', 'pinyin'], 'string', 'max' => 40],
            [['citycode', 'zipcode'], 'string', 'max' => 7],
            [['lng', 'lat'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'name' => '省市区名称',
            'parentid' => '上级ID',
            'shortname' => '简称',
            'leveltype' => '级别:0,中国；1，省分；2，市；3，区、县',
            'citycode' => '城市代码',
            'zipcode' => '邮编',
            'lng' => '经度',
            'lat' => '纬度',
            'pinyin' => '拼音',
            'status' => 'Status',
        ];
    }

    public function getParent(){
        return $this->hasOne(self::className(),['id'=>'parentid'])->where('status<>0');
    }

    public function getChilds(){
        return $this->hasMany(self::className(),['parentid'=>'id'])->where('status<>0');
    }

    public static function GetAllProvince(){
        return self::find()->where("status<>'0' and leveltype=1")->asArray()->all();
    }
    public static function GetDefaultCity(){
        return self::find()->where("status<>'0' and leveltype=2 and parentid=320000")->asArray()->all();
    }

    public static function GetDefaultDistrict(){
        return self::find()->where("status<>'0' and leveltype=3 and parentid=320400")->asArray()->all();
    }

    //省
    public static function getProvince(){
        $model=self::find()->where(['leveltype'=>1,'status'=>'1'])->asArray()->all();
        return empty($model)?[]:$model;
    }

    //市
    public static function getCity($p_id){
        $model=Zone::find()->where(['leveltype'=>2,'status'=>'1','parentid'=>$p_id])->asArray()->all();
        return empty($model)?[]:$model;
    }

    //区
    public static function getDistrict($c_id){
        $model=Zone::find()->where(['leveltype'=>3,'status'=>'1','parentid'=>$c_id])->asArray()->all();
        return empty($model)?[]:$model;
    }

    //根据id查找地名
    public static function getDetailName($id){
        $model=Zone::find()->where(['id'=>$id])->asArray()->one();
        return empty($model)?'':$model['name'];

    }

    //根据地名查找对应id
    public static function getDetailId($name){
        $model=Zone::find()->where(['name'=>$name])->asArray()->one();
        return empty($model)?'':$model['id'];
    }

    //根据id取经纬度
    public static function getLngLat($name){
        $model=Zone::find()->where(['name'=>$name])->asArray()->one();
        return empty($model)?'':$model;
    }
}
