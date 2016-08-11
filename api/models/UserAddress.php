<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "user_address".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $get_person
 * @property string $get_phone
 * @property string $region
 * @property string $address
 * @property integer $lat
 * @property integer $lng
 * @property string $tag
 * @property integer $is_default
 * @property integer $status
 * @property string $created_time
 * @property string $updated_time
 *
 * @property UserInfo $u
 */
class UserAddress extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'lat', 'lng', 'is_default', 'status'], 'integer'],
            [['created_time', 'updated_time'], 'safe'],
            [['get_person'], 'string', 'max' => 128],
            [['get_phone', 'tag'], 'string', 'max' => 32],
            [['region', 'address'], 'string', 'max' => 255],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => UserInfo::className(), 'targetAttribute' => ['uid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键id',
            'uid' => '用户id',
            'get_person' => 'Get Person',
            'get_phone' => '手机号',
            'region' => '地区',
            'address' => '详细地址',
            'lat' => '纬度',
            'lng' => '经度',
            'tag' => '标签',
            'is_default' => '是否为默认地址 0否 1是',
            'status' => '状态 0删除 1正常',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserInfo()
    {
        return $this->hasOne(UserInfo::className(), ['id' => 'uid']);
    }
}
