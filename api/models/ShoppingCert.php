<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "shopping_cert".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $gid
 * @property integer $amount
 * @property string $total_price
 *
 * @property UserInfo $u
 */
class ShoppingCert extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shopping_cert';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'gid', 'amount'], 'integer'],
            [['total_price'], 'number'],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => UserInfo::className(), 'targetAttribute' => ['uid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'uid' => '用户id',
            'gid' => '产品id',
            'amount' => '数量',
            'total_price' => '总价',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getU()
    {
        return $this->hasOne(UserInfo::className(), ['id' => 'uid']);
    }
}