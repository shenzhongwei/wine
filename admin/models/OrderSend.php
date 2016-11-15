<?php

namespace admin\models;


use Yii;


class OrderSend extends OrderInfo
{

    public $send_now;
    public $id_str;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['send_id','send_now'], 'integer'],
            [['send_id'], 'required','message'=>'请选择配送员'],
            [['send_now'], 'required','message'=>'请选择是否马上配送'],
            [['id_str'], 'required','message'=>'派送单异常'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'send_id' => '配送员',
            'send_now' => '是否马上配送',
            'id_str' => '配送单',
        ];
    }

}
