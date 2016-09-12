<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\EmployeeInfo;
use admin\models\MerchantInfoSearch;
use admin\models\ShopSearch;
/**
 * EmployeeInfoSearch represents the model behind the search form about `admin\models\EmployeeInfo`.
 */
class EmployeeInfoSearch extends EmployeeInfo
{
    public function rules()
    {
        return [
            [['id', 'type', 'owner_id', 'register_at', 'status'], 'integer'],
            [['name', 'phone'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = EmployeeInfo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([ 'owner_id' => $this->owner_id]);
        if($this->type!=''){  //查询配送人员类型：非全部
            $query->andFilterWhere(['type' => $this->type]);
        }
        if($this->status!=''){ //查询配送人员状态：非全部
            $query->andFilterWhere(['status' => $this->status]);
        }

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'phone', $this->phone]);

        return $dataProvider;
    }

    //配送人员所属上级名称
    public static function getOwnerName($data){
        if($data->type==0){ //商家
            return '<h4 style="color:#ff674c">'.MerchantInfoSearch::getOneMerchant($data->owner_id).'</h4>';
        }elseif($data->type==1){  //门店
            return '<h4 style="color:#ff674c">'.ShopSearch::getOneShopname($data->owner_id).'</h4>';
        }else{
            return '';
        }
    }

    //配送人员的当前状态
    public static function getEmploySattus($data){
        switch($data->status){
            case 0: return '<p><span class="label label-default"><i class="fa fa-times"></i> 已 删</span></p>'; break;
            case 1: return '<p><span class="label label-primary"><i class="fa fa-check"></i> 正常</span></p>'; break;
            case 2: return '<p><span class="label label-warning"><i class="fa fa-warning"></i> 繁忙</span></p>';  break;
            case 3: return '<p><span class="label label-danger"><i class="fa fa-remove"></i> 下岗</span></p>'; break;
            default:break;
        }
    }
}
