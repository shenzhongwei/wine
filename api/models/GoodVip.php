<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "good_vip".
 *
 * @property integer $id
 * @property integer $gid
 * @property string $price
 * @property integer $is_active
 *
 * @property GoodInfo $g
 */
class GoodVip extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'good_vip';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gid', 'is_active'], 'integer'],
            [['price'], 'number'],
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
            'gid' => '商品id',
            'price' => '会员专享价',
            'is_active' => '是否上架',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getG()
    {
        return $this->hasOne(GoodInfo::className(), ['id' => 'gid']);
    }
}
