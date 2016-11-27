<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * MerchantInfoSearch represents the model behind the search form about `admin\models\MerchantInfo`.
 */
class MerchantInfoSearch extends MerchantInfo
{

    public function rules()
    {
        return [
            [['is_active','wa_status'], 'integer'],
            [['name', 'region', 'contacter', 'phone', 'city', 'district','registe_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }


    public function search($params)
    {
        $query = MerchantInfo::find()->joinWith(['wa'])->addSelect(['merchant_info.*','IFNULL(wine_admin.wa_lock,1) as wa_status']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $sort = $dataProvider->getSort();
        $sort->attributes['wa_status'] = [
            'asc' => ['wa_status' => SORT_ASC],
            'desc' => ['wa_status' => SORT_DESC],
            'label' => 'wa_status',
        ];
        $sort->defaultOrder=[
            'is_active'=>SORT_DESC,
            'wa_status'=>SORT_ASC,
        ];
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'is_active' => $this->is_active,
            'city'=>$this->city,
            'district'=>$this->district,
            'region'=>$this->region,
        ]);
        if($this->wa_status==1){
            $query->andWhere('wine_admin.wa_lock=1 or wine_admin.wa_lock is null');
        }
        if($this->wa_status==='0'){
            $query->andFilterWhere(['wine_admin.wa_lock'=>$this->wa_status]);
        }
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'phone', $this->name])
            ->andFilterWhere(['like', 'contacter', $this->name]);
        $query->andFilterWhere(['>=',"FROM_UNIXTIME(registe_at,'%Y年%m月%d日')",$this->registe_at]);
        return $dataProvider;
    }

    /*查询所有商户名称*/
    public static function getAllMerchant(){
        $model=MerchantInfo::find()->asArray()->all();

        $data=ArrayHelper::getColumn($model,function($element){
            return  $element['name'];
        });
        return $data;
    }

    public static function getOneMerchant($id){
        return MerchantInfo::findOne($id)->name;
    }


}
