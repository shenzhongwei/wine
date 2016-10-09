<?php

use kartik\tabs\TabsX;

/**
 * * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\BrandSearch $searchModel
 * @var yii\data\ActiveDataProvider $smellData
 * @var admin\models\SmellSearch $smellSearch
 * @var admin\models\GoodType $model
 */
$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '子检索'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$brand = $this->render('brand',['model'=>$model,'searchModel'=>$searchModel,'dataProvider'=>$dataProvider]);
$smell = $this->render('smell',['model'=>$model,'searchModel'=>$smellSearch,'dataProvider'=>$smellData]);
$items = [
    [
        'label'=>'<button class="btn btn-link btn-xs" id="brand">品 牌</button>',
        'content'=>$brand,
        'active'=>$key == 'brand' ? true:false,
    ],
    [
        'label'=>'<button class="btn btn-link btn-xs" id="smell">香 型</button>',
        'content'=>$smell,
        'active'=>$key == 'brand' ? true:false,
    ],
    [
        'label'=>'<button class="btn btn-link btn-xs">产 地</button>',
    ],
    [
        'label'=>'<button class="btn btn-link btn-xs">价格区间</button>',
    ],
    [
        'label'=>'<button class="btn btn-link btn-xs">颜 色</button>',
    ],
    [
        'label'=>'<button class="btn btn-link btn-xs">品 种</button>',
    ],
    [
        'label'=>'<button class="btn btn-link btn-xs">干 型</button>',
    ],
    [
        'label'=>'<button class="btn btn-link btn-xs">规 格</button>',
    ],
    [
        'label'=>'<button class="btn btn-link btn-xs">国 家</button>',
    ],
    [
        'label'=>'<button class="btn btn-link btn-xs">类 型</button>',
    ],
];
?>
<div class="good-type-view col-sm-12">
    <div class="panel panel-info">
        <div class="panel-heading">
            <?= '检索：'.$model->name ?>
        </div>
        <div class="panel-body">
        <?php
    echo TabsX::widget([
        'items'=>$items,
        'position'=>TabsX::POS_ABOVE,
        'encodeLabels'=>false,
        'align'=>TabsX::SIZE_LARGE,
//        'height'=>TabsX::SIZE_MEDIUM,
        'bordered'=>false,
    ]);
    ?>
            </div>
        </div>
</div>