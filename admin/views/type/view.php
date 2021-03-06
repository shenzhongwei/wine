<style>
    .select2-dropdown--below{
        z-index: 99999;
    }
</style>
<?php

use kartik\tabs\TabsX;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var admin\models\BrandSearch $searchModel
 * @var yii\data\ActiveDataProvider $smellData
 * @var admin\models\SmellSearch $smellSearch
 * @var yii\data\ActiveDataProvider $bootData
 * @var admin\models\BootSearch $bootSearch
 * @var yii\data\ActiveDataProvider $priceData
 * @var yii\data\ActiveDataProvider $colorData
 * @var admin\models\ColorSearch $colorSearch
 * @var yii\data\ActiveDataProvider $breedData
 * @var admin\models\BreedSearch $breedSearch
 * @var yii\data\ActiveDataProvider $dryData
 * @var admin\models\DrySearch $drySearch
 * @var yii\data\ActiveDataProvider $volumData
 * @var admin\models\ModelSearch $volumSearch
 * @var yii\data\ActiveDataProvider $countryData
 * @var admin\models\CountrySearch $countrySearch
 * @var yii\data\ActiveDataProvider $styleData
 * @var admin\models\StyleSearch $styleSearch
 * @var admin\models\GoodType $model
 * @var string $key
 */
$this->title = ' 检索：'.$model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '子检索'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$brand = $this->render('brand',['model'=>$model,'searchModel'=>$searchModel,'dataProvider'=>$dataProvider]);
$smell = $this->render('smell',['model'=>$model,'searchModel'=>$smellSearch,'dataProvider'=>$smellData]);
$boot = $this->render('boot', ['model' => $model, 'searchModel' => $bootSearch, 'dataProvider' => $bootData]);
$price = $this->render('price',['model'=>$model,'dataProvider'=>$priceData]);
$color = $this->render('color', ['model' => $model, 'searchModel' => $colorSearch, 'dataProvider' => $colorData]);
$breed = $this->render('breed', ['model' => $model, 'searchModel' => $breedSearch, 'dataProvider' => $breedData]);
$dry = $this->render('dry', ['model' => $model, 'searchModel' => $drySearch, 'dataProvider' => $dryData]);
$volum = $this->render('volum', ['model' => $model, 'searchModel' => $volumSearch, 'dataProvider' => $volumData]);
$country = $this->render('country', ['model' => $model, 'searchModel' => $countrySearch, 'dataProvider' => $countryData]);
$style = $this->render('style', ['model' => $model, 'searchModel' => $styleSearch, 'dataProvider' => $styleData]);
$items = [
    [
        'label' => '品 牌',
        'content'=>$brand,
        'active'=>$key == 'brand' ? true:false,
    ],
    [
        'label' => '香 型',
        'content'=>$smell,
        'active' => $key == 'smell' ? true : false,
    ],
    [
        'label' => '产 地',
        'content' => $boot,
        'active' => $key == 'boot' ? true : false,
    ],
    [
        'label'=>'价格区间',
        'content'=>$price,
        'active'=>$key == 'price' ? true:false,
    ],
    [
        'label' => '颜 色',
        'content' => $color,
        'active' => $key == 'color' ? true : false,
    ],
    [
        'label' => '品 种',
        'content' => $breed,
        'active' => $key == 'breed' ? true : false,
    ],
    [
        'label' => '干 型',
        'content' => $dry,
        'active' => $key == 'dry' ? true : false,
    ],
    [
        'label' => '规 格',
        'content' => $volum,
        'active' => $key == 'volum' ? true : false,
    ],
    [
        'label' => '国 家',
        'content' => $country,
        'active' => $key == 'country' ? true : false,
    ],
    [
        'label' => '类 型',
        'content' => $style,
        'active' => $key == 'style' ? true : false,
    ],
];
?>
<div class="good-type-view">
    <div class="panel panel-success">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-th-list"></span><?= $this->title ?>
        </div>
        <div class="panel-body">
        <?php
    echo TabsX::widget([
        'items'=>$items,
        'position'=>TabsX::POS_ABOVE,
        'encodeLabels'=>false,
        'align'=>TabsX::SIZE_LARGE,
        'bordered'=>false,
        'options' => [
            'style' => 'width:100%'
        ]
    ]);
    ?>
            </div>
        </div>
</div>