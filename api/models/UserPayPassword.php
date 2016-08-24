<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "user_pay_password".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $password
 *
 * @property UserInfo $u
 */
class UserPayPassword extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_pay_password';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid'], 'required'],
            [['uid'], 'integer'],
            [['password'], 'string', 'max' => 32],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => UserInfo::className(), 'targetAttribute' => ['uid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'uid' => '用户',
            'password' => '密码',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getU()
    {
        return $this->hasOne(UserInfo::className(), ['id' => 'uid']);
    }
}
