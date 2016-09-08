<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "dics".
 *
 * @property string $type
 * @property integer $id
 * @property string $name
 */
class Dics extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dics';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['type', 'name'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => 'Type',
            'id' => 'ID',
            'name' => 'Name',
        ];
    }
}
