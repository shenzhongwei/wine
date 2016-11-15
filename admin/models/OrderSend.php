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
            [['sid','send_now'], 'integer'],
            [['sid','send_now'], 'required'],
        ];
    }

}
