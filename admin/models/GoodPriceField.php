<?php

namespace admin\models;

use common\helpers\ArrayHelper;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "good_price_field".
 *
 * @property integer $id
 * @property integer $type
 * @property string $discription
 *
 * @property GoodType $type0
 */
class GoodPriceField extends \yii\db\ActiveRecord
{
    public $start;
    public $end;
    public $end_rep;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'good_price_field';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type','start'], 'integer'],
            [['start','type'],'required'],
            ['start', 'compare', 'compareValue' => 0, 'operator' => '>='],
//            ['price','compare','compareAttribute'=>'pro_price','operator'=>'>='],
            [['end','end_rep','start'],'validEnd'],
            [['discription'], 'string', 'max' => 200],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => GoodType::className(), 'targetAttribute' => ['type' => 'id']],
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
            'discription' => '区间',
            'start'=>'开始金额',
            'end_rep'=>'结束金额',
            'end'=>'结束金额',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType0()
    {
        return $this->hasOne(GoodType::className(), ['id' => 'type']);
    }

    /**
     * valid规则
     */
    public function validStart(){
            if($this->end!='+∞'&&$this->start>=$this->end){
                $this->addError('start','开始金额必须小于结束金额');
            }
            $query = self::find()->where(['type'=>$this->type]);
            if(!empty($this->id)){
                $query->andWhere('id<>'.$this->id);
            }
            $query->andWhere("(SUBSTR(SUBSTRING_INDEX(discription,',',1),2)<=$this->start and (SUBSTRING_INDEX(SUBSTRING_INDEX(discription,',',-1),']',1)>$this->start
         or SUBSTRING_INDEX(SUBSTRING_INDEX(discription,',',-1),']',1)='+∞'))");
            $model = $query->one();
            if(!empty($model)){
                $this->addError('start','该金额与已存在的区间存在冲突，请重新输入');
            }
    }

    public function validEnd(){
            if(empty($this->start)){
                $this->start = 0;
            }
            if($this->end<=0&&is_numeric($this->end)){
                $this->addError('end','结束金额必须为正整数');
            }
            if($this->start>=$this->end&&is_numeric($this->end)){
                $this->addError('end','结束金额必须大于开始金额');
            }
            $query = self::find()->where("type=$this->type");
            if(!empty($this->id)){
                $query->andWhere('id<>'.$this->id);
            }
            if($this->end !='+∞'){
                $query->andWhere("(SUBSTR(SUBSTRING_INDEX(discription,',',1),2)>=$this->start 
            AND SUBSTR(SUBSTRING_INDEX(discription,',',1),2)<$this->end) OR
            ((SUBSTRING_INDEX(SUBSTRING_INDEX(discription,',',-1),']',1)>$this->start AND 
            SUBSTRING_INDEX(SUBSTRING_INDEX(discription,',',-1),']',1)<$this->end AND 
            SUBSTRING_INDEX(SUBSTRING_INDEX(discription,',',-1),']',1)<>'+∞') OR 
            (SUBSTRING_INDEX(SUBSTRING_INDEX(discription,',',-1),']',1)='+∞' AND 
            SUBSTR(SUBSTRING_INDEX(discription,',',1),2)<$this->end))");
            }else{
                $query->andWhere("SUBSTRING_INDEX(SUBSTRING_INDEX(discription,',',-1),']',1)>$this->start OR 
            SUBSTRING_INDEX(SUBSTRING_INDEX(discription,',',-1),']',1)='+∞'");
            }
            $model = $query->one();
            if(!empty($model)){
                $this->addError('end','该金额与已存在的区间存在冲突，请重新输入');
            }
    }

    public static function GetAllTypes(){
        return ArrayHelper::map(GoodType::find()->all(),'id','name');
    }


    public function search($id)
    {
        $query = self::find()->select(["SUBSTRING_INDEX(SUBSTRING_INDEX(discription,',',-1),']',1) as end",
        "SUBSTR(SUBSTRING_INDEX(discription,',',1),2) as start",'type','id'])->where(['type' => $id])->orderBy('`start`+0');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort = false;
        return $dataProvider;
    }
}
