<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use kartik\widgets\DepDrop;
/**
 * @var yii\web\View $this
 * @var admin\models\MessageList $model
 * @var yii\widgets\ActiveForm $form
 */
if($wa_type==2 ){ //系统管理员
    $type_list=['1'=>'系统消息','4'=>'商品通知'];

}elseif($wa_type==3){//商家管理员
    $type_list=['4'=>'商品通知'];
}

?>

<div class="message-list-form">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]);
          echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [

            'type_id'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>kartik\select2\Select2::className(),
                'options'=>[
                    'data'=>$type_list,
                    'options'=>['placeholder'=>'请选择消息类型'],
                    'pluginOptions'=>['allowClear'=>true]
                ]
            ],

            'own_id'=>['type'=>Form::INPUT_WIDGET,'widgetClass'=>DepDrop::className(),
                'options'=>[
                    'type' => DepDrop::TYPE_SELECT2,
                    'data'=>[],
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                    'pluginOptions'=>[
                        'placeholder'=>'请选择对应类型的所属人',
                        'depends'=>['messagelist-type_id'],
                        'url' =>\yii\helpers\Url::toRoute(['message/relation-name']),
                        'loadingText' => '查找...',
                    ]
                ]
            ],

            'target'=>['type'=> Form::INPUT_WIDGET,'widgetClass'=>kartik\select2\Select2::className(),
                'options'=>[
                    'data'=>\admin\models\Dics::getMessageToUrl(),
                    'options'=>['placeholder'=>'请选择跳转页面'],
                    'pluginOptions'=>['allowClear'=>true]
                ]
            ],

            'title'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'输入消息标题', 'maxlength'=>50]],

            'content'=>['type'=> Form::INPUT_TEXTAREA, 'options'=>['placeholder'=>'输入消息内容,最多80个字符', 'maxlength'=>255,'onkeyup'=>'words_del();']],

        ]

    ]);
    ?>
    <p style="margin: 5px auto;text-align: center">
    <?=Html::submitButton($model->isNewRecord ? Yii::t('app', '创建') : Yii::t('app', '更新'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);?>
    <?= Html::resetButton('重置', ['class' => 'btn btn-warning']) ?>
    </p>

    <?php ActiveForm::end(); ?>

</div>
<script>
    function words_del(){
        var node=$("#messagelist-content");
        var curLength=node.val().length;
        if(curLength>80){
            var num=node.val().substr(0,80);
            node.val(num);
        }
    }
</script>