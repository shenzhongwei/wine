<?php

namespace admin\models;

use Yii;
use yii\base\Model;

/**
 * Class Push
 * @package admin\models
 * 推送类
 */
class Push extends Model
{
    public $content;

    public function rules()
    {
        return [
            [['content'],'required','message'=>'请填写推送内容']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'content' => '推送内容',
        ];
    }
}
