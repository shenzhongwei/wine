<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "user_ticket".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $pid
 * @property integer $start_at
 * @property integer $end_at
 * @property integer $status
 *
 * @property PromotionInfo $p
 * @property UserInfo $u
 * @property TicketInout $inout
 */
class UserTicket extends \yii\db\ActiveRecord
{

    public $order;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_ticket';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'pid', 'start_at', 'end_at', 'status'], 'integer'],
            [['pid'], 'exist', 'skipOnError' => true, 'targetClass' => PromotionInfo::className(), 'targetAttribute' => ['pid' => 'id']],
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
            'uid' => '用户id',
            'pid' => '促销id',
            'start_at' => '有效开始时间',
            'end_at' => '结束时间',
            'status' => '1正常 0过期 2已使用',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getP()
    {
        return $this->hasOne(PromotionInfo::className(), ['id' => 'pid'])->where('promotion_info.id>0');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getU()
    {
        return $this->hasOne(UserInfo::className(), ['id' => 'uid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInout()
    {
        return $this->hasOne(TicketInout::className(), ['tid' => 'id']);
    }


    /**
     * @param $user_id
     * @return bool
     * @throws \yii\db\Exception
     * 自动过期优惠券
     */
    public static function AutoOverTimeTicket($user_id){
        $userTicket = self::find()->where(['and','uid='.$user_id,'start_at>0','end_at>0','status=1','end_at<'.time()])->one();
        if(!empty($userTicket)){
            $sql = "UPDATE user_ticket SET status=0 WHERE uid=$user_id AND start_at>0 AND end_at>0 AND status=1 AND end_at<".time();
            $row = Yii::$app->db->createCommand($sql)->execute();
            if(!empty($row)){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }
}
