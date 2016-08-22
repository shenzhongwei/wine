<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "good_price_field".
 *
 * @property integer $id
 * @property string $discription
 */
class PriceField extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'good_price_field';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['discription'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键id',
            'discription' => '区间',
        ];
    }
}
