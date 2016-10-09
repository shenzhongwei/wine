<?php

/* @var $this \yii\web\View */
/* @var $content string */

// use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use kartik\alert\Alert;

// AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <title>酒双天后台管理->主页</title>
    <meta name="keywords" content="酒双天,后台管理">
    <meta name="description" content="">
    <!--[if lt IE 9]>
    <meta http-equiv="refresh" content="0;ie.html" />
    <![endif]-->
    <link rel="shortcut icon" href="favicon.ico">
    <?=Html::cssFile('@web/css/bootstrap.min14ed.css')?>
    <?=Html::cssFile('@web/css/bootstrap.min.css')?>
    <?=Html::cssFile('@web/css/bootstrap.css')?>
    <?=Html::cssFile('@web/css/font-awesome.min93e3.css')?>
    <?=Html::cssFile('@web/css/plugins/cropper/cropper.min.css')?>
    <?=Html::cssFile('@web/css/plugins/switchery/switchery.css')?>
    <?=Html::cssFile('@web/css/animate.min.css')?>
    <?=Html::cssFile('@web/css/style.min862f.css')?>
    <?=Html::cssFile('@web/css/site.css')?>
    <?=Html::cssFile('@web/css/plugins/iCheck/custom.css')?>
    <?=Html::cssFile('@web/css/plugins/datapicker/datepicker3.css')?>
    <?=Html::jsFile('@web/js/jquery.min.js')?>
    <?=Html::jsFile('@web/js/contabs.min.js')?>
    <?=Html::jsFile('@web/js/wine/wine.js')?>
    <?=Html::jsFile('@web/js/plugins/switchery/switchery.js')?>
    <?=Html::jsFile('@web/js/plugins/cropper/cropper.min.js')?>
    <?=Html::jsFile('@web/js/demo/form-advanced-demo.min.js')?>
    <?=Html::jsFile('@web/js/plugins/layer/layer.js')?>
    <?=Html::jsFile('@web/js/plugins/suggest/bootstrap-suggest.min.js')?>
    <?=Html::jsFile('@web/js/bootstrap.js')?>
    <?=Html::jsFile('@web/js/content.min.js')?>
    <?=Html::jsFile('@web/js/plugins/fancybox/jquery.fancybox.js'); ?>
<!--    --><?//=Html::jsFile('@web/js/bootstrap.min.js')?>
    <?php $this->head() ?>
</head>
<body class="fixed-sidebar full-height-layout gray-bg">
<?php if (Yii::$app->session->hasFlash('success')) {
    echo Alert::widget([
        'type' => Alert::TYPE_SUCCESS,
        'title' => null,
        'icon' => 'glyphicon glyphicon-ok-sign',
        'body' => Yii::$app->session->getFlash('success'),
        'showSeparator' => true,
        'delay' => 3000
    ]);
}
if (Yii::$app->session->hasFlash('danger')) {
    echo Alert::widget([
        'type' => Alert::TYPE_WARNING,
        'title' => null,
        'icon' => 'glyphicon glyphicon-exclamation-sign',
        'body' => Yii::$app->session->getFlash('danger'),
        'showSeparator' => true,
        'delay' => 3000,
    ]);
}
?>
<?php $this->beginBody() ?>
<?php $this->endBody() ?>
<?= $content ?>
</body>
</html>
<?php $this->endPage() ?>