<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "user_address".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $get_person
 * @property string $get_phone
 * @property string $province
 * @property string $city
 * @property string $district
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
 * @property OrderInfo[] $orderInfos
 * @property UserInfo $u
 */
class UserAddress extends \yii\db\ActiveRecord
{

    public $distance;
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
            [['get_person', 'province', 'city', 'district'], 'string', 'max' => 128],
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
            'id' => 'ID',
            'uid' => 'Uid',
            'get_person' => 'Get Person',
            'get_phone' => 'Get Phone',
            'province' => 'Province',
            'city' => 'City',
            'district' => 'District',
            'region' => 'Region',
            'address' => 'Address',
            'lat' => 'Lat',
            'lng' => 'Lng',
            'tag' => 'Tag',
            'is_default' => 'Is Default',
            'status' => 'Status',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderInfos()
    {
        return $this->hasMany(OrderInfo::className(), ['aid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getU()
    {
        return $this->hasOne(UserInfo::className(), ['id' => 'uid']);
    }
}
