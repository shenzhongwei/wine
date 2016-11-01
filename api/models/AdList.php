<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "ad_list".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $target_id
 * @property integer $postion
 * @property string $pic
 * @property string $pic_url
 * @property integer $is_show
 */
class AdList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ad_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'target_id', 'is_show','postion'], 'integer'],
            [['pic', 'pic_url'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键id',
            'type' => '类型',
            'target_id' => '对应id',
            'pic' => '广告图片',
            'postion'=>'广告位置',
            'pic_url' => '图片链接网站',
            'is_show' => '是否显示,1是，0否',
        ];
    }

    public static function Search($colum=[]){
        return self::find()->select($colum)->where(['is_show'=>1]);
    }
}
