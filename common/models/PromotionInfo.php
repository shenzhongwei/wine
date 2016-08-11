<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "promotion_info".
 *
 * @property integer $pi_id
 * @property string $ci_id
 * @property integer $pi_type
 * @property string $pi_group
 * @property integer $gi_id
 * @property string $pi_name
 * @property string $pi_text
 * @property integer $pi_amount
 * @property string $pi_money
 * @property string $pi_discount
 * @property string $pi_conditional
 * @property string $pi_sdate
 * @property string $pi_edate
 * @property integer $pi_store
 * @property integer $pi_porm
 * @property string $pi_cycle
 * @property integer $pi_calc
 * @property integer $pi_time
 * @property integer $pi_order
 * @property integer $pi_status
 */
class PromotionInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'promotion_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ci_id', 'pi_type', 'gi_id', 'pi_amount', 'pi_sdate', 'pi_edate', 'pi_store', 'pi_porm', 'pi_calc', 'pi_time', 'pi_order', 'pi_status'], 'integer'],
            [['pi_money', 'pi_discount', 'pi_conditional', 'pi_cycle'], 'number'],
            [['pi_group', 'pi_name'], 'string', 'max' => 32],
            [['pi_text'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pi_id' => 'Pi ID',
            'ci_id' => 'Ci ID',
            'pi_type' => 'Pi Type',
            'pi_group' => 'Pi Group',
            'gi_id' => 'Gi ID',
            'pi_name' => 'Pi Name',
            'pi_text' => 'Pi Text',
            'pi_amount' => 'Pi Amount',
            'pi_money' => 'Pi Money',
            'pi_discount' => 'Pi Discount',
            'pi_conditional' => 'Pi Conditional',
            'pi_sdate' => 'Pi Sdate',
            'pi_edate' => 'Pi Edate',
            'pi_store' => 'Pi Store',
            'pi_porm' => 'Pi Porm',
            'pi_cycle' => 'Pi Cycle',
            'pi_calc' => 'Pi Calc',
            'pi_time' => 'Pi Time',
            'pi_order' => 'Pi Order',
            'pi_status' => 'Pi Status',
        ];
    }
}
