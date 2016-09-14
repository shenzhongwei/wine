<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\select2\Select2;
use admin\models\PromotionType;
use admin\models\PromotionInfo;
use admin\models\Dics;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use kartik\widgets\DatePicker;
/**
 * @var yii\web\View $this
 * @var admin\models\PromotionInfo $model
 * @var yii\widgets\ActiveForm $form
 */

//优惠分类
$typemodel=PromotionType::find()->select(['id','name'])->asArray()->all();
$res=[];
foreach($typemodel as $k=>$v){
    $res[$v['id']]=$v['name'];
}

?>
<style>
    .showtime{display: none;}
</style>
<div class="promotion-info-form" style="margin-top: 5px;">
    <?php
    $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]);
    echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [

            'name'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'输入活动名称', 'maxlength'=>128]],

            'discount'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'输入折扣', 'maxlength'=>11]],

            'condition'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'输入条件', 'maxlength'=>11]],

            'pt_id'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>select2::className(),
                'options'=>[
                    'data'=>$res,
                    'options'=>['placeholder'=>'请选择优惠类型'],
                    'pluginOptions'=>['allowClear'=>true]
                ]
            ],


            'limit'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>select2::className(),
                'options'=>[
                    'data'=>['1'=>'平台通用'],
                    //'data'=>Dics::getPromotionRange(),
                    'options'=>['placeholder'=>'请选择适用范围'],
                    'pluginOptions'=>['allowClear'=>true]
                ]
            ],

            'target_id'=>['type'=>Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                'options'=>[
                    'type' => DepDrop::TYPE_SELECT2,
                    'data'=>$model->limit===''?[]:PromotionInfo::getTargetsRange($model->limit),
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                    'pluginOptions'=>[
                        'placeholder'=>'选择对象',
                        'depends'=>['promotioninfo-limit'],
                        'url' =>Url::toRoute(['promotion/targets']),
                        'loadingText' => '查找...',
                    ]
                ]
            ],

            'time'=>['type'=> Form::INPUT_TEXT, 'options'=>['输入次数']],

            'valid_circle'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>select2::className(),
                'options'=>[
                    'data'=>['0'=>'永久有效','-1'=>'非永久有效'],
                    'options'=>['placeholder'=>$model->valid_circle==0?'请输入有效期':($model->valid_circle.'天')],
                    'pluginOptions'=>['allowClear'=>true]
                ]
            ],
        ]
    ]);

    ?>
    <div class="showtime">
        <?= $form->field($model, 'start_from')->widget(DatePicker::classname(),[
            'options' => ['placeholder' =>''],
            'pluginOptions' => [
                'todayHighlight' => true,
                'format' => 'yyyy-mm-dd',
            ]
        ]) ?>

        <?= $form->field($model, 'end_to')->widget(DatePicker::classname(),[
            'options' => ['placeholder' =>''],
            'pluginOptions' => [
                'todayHighlight' => true,
                'format' => 'yyyy-mm-dd',
            ]
        ]) ?>
    </div>

    <p style="margin: 0 auto;text-align: center;margin-bottom: 2px;">
        <?=Html::submitButton($model->isNewRecord ? Yii::t('app', '创建') : Yii::t('app', '更新'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);?>
        <?=Html::a('返回', 'javascript:history.go(-1);location.reload()', ['class' => 'btn btn-primary','style'=>'margin-left:10px']);?>
    </p>
    <?php  ActiveForm::end(); ?>

</div>
<script>
    $(function(){
        $('select#promotioninfo-valid_circle').change(function(){
            var checkValue=$(this).val(); //获取value值
            if(checkValue==-1){
                $('.showtime').show();
            }else{
                $('.showtime').hide();
            }
        });
    })
</script>