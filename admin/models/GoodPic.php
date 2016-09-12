<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "good_pic".
 *
 * @property integer $id
 * @property integer $gid
 * @property string $pic
 * @property integer $status
 *
 * @property GoodInfo $g
 */
class GoodPic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'good_pic';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gid', 'status'], 'integer'],
            [['pic'], 'string', 'max' => 250],
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
            'gid' => '产品id',
            'pic' => '图片地址',
            'status' => '状态',
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
