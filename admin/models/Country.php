<?php

namespace admin\models;

use common\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "country".
 *
 * @property integer $id
 * @property string $name
 * @property string $zh_name
 * @property string $code
 * @property string $code2
 * @property integer $is_show
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'zh_name'], 'required'],
            [['is_show'], 'integer'],
            [['name', 'zh_name'], 'string', 'max' => 50],
            [['code', 'code2'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'zh_name' => 'Zh Name',
            'code' => 'Code',
            'code2' => 'Code2',
            'is_show' => '是否显示 1 显示 0 不显示',
        ];
    }

    public static function GetAllCountry(){
        return ArrayHelper::map(self::find()->where("is_show=1")->all(),'zh_name','zh_name');
    }
}
