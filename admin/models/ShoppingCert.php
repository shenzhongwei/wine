<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "shopping_cert".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $gid
 * @property integer $amount
 *
 * @property UserInfo $u
 * @property GoodInfo $g
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
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => UserInfo::className(), 'targetAttribute' => ['uid' => 'id']],
            [['gid'], 'exist', 'skipOnError' => true, 'targetClass' => GoodInfo::className(), 'targetAttribute' => ['gid' => 'id']],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getU()
    {
        return $this->hasOne(UserInfo::className(), ['id' => 'uid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getG()
    {
        return $this->hasOne(GoodInfo::className(), ['id' => 'gid']);
    }

    /**
     * @inheritdoc
     * @return ShoppingCertQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ShoppingCertQuery(get_called_class());
    }
}
