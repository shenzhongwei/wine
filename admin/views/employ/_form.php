<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use admin\models\ShopInfo;
use admin\models\ShopSearch;
use admin\models\MerchantInfo;
use admin\models\MerchantInfoSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var admin\models\EmployeeInfo $model
 * @var yii\widgets\ActiveForm $form
 */
 function getowner($model){
    switch($model->type){
        case 0: //商家
            $query=MerchantInfo::find()->where(['is_active'=>1])->all();
            break;
        case 1: //门店
            $query=ShopInfo::find()->where(['is_active'=>1])->all();
            break;
    }
     $results=[];
    if(!empty($query)){
        foreach($query as $k=>$v){
            $results[$v->id]=$v->name;
        }
    }
    return $results;
}
?>

<div class="employee-info-form" style="margin-top: 5px;">

    <?php $form = ActiveForm::begin([
        'type'=>ActiveForm::TYPE_HORIZONTAL,
    ]);?>
    <?php echo Form::widget([
            'model' => $model,
            'form' => $form,
            'columns' => 1,
            'columnSize'=>'sm',
            'attributes' => [

                'name'=>['type'=> Form::INPUT_TEXT, 'options'=>['maxlength'=>25,]],

                'phone'=>['type'=> Form::INPUT_TEXT, 'options'=>['maxlength'=>11]],

                'type'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>select2::className(),
                    'options'=>[
                        'data'=>['0'=>'商户','1'=>'门店'],
                        'options'=>['placeholder'=>'请选择类型'],
                        'pluginOptions'=>['allowClear'=>true]
                    ]
                ],

                'owner_id'=>['type'=>Form::INPUT_WIDGET,'label'=>'上级商家/门店名称','widgetClass'=>DepDrop::className(),
                    'options'=>[
                        'type' => DepDrop::TYPE_SELECT2,
                        'data'=>$model->type===''? []:\admin\models\EmployeeInfo::getOwners($model->type),
                        'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                        'pluginOptions'=>[
                            'placeholder'=>'请选择所属商家/门店',
                            'depends'=>['employeeinfo-type'],
                            'url' =>Url::toRoute(['employ/owners']),
                            'loadingText' => '查找...',
                        ]
                    ]
                ],
            ]
    ]);

    ?>

    <p style="margin: 0 auto;text-align: center;margin-bottom: 2px;">
        <?=Html::submitButton($model->isNewRecord ? Yii::t('app', '创建') : Yii::t('app', '更新'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);?>
        <?=Html::a('返回', 'javascript:history.go(-1);location.reload()', ['class' => 'btn btn-primary','style'=>'margin-left:10px']);?>
    </p>
    <?php ActiveForm::end(); ?>

</div>
