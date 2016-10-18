<?php
use yii\helpers\Url;

/**
 * @var string $code
 * @var string $message
 * @var string $name
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1, user-scalable=no" name="viewport">
	<meta content="yes" name="apple-mobile-web-app-capable">
	<meta content="yes" name="apple-touch-fullscreen">
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<meta content="telephone=no" name="format-detection">
	<meta name="renderer" content="webkit">
	<script type="application/javascript" src="<?=Url::to('@web/js/recommend/flexible_css.debug.js') ?>"></script>
	<script type="application/javascript" src="<?=Url::to('@web/js/recommend/flexible.debug.js') ?>"></script>
	<title>51酒易购分享</title>
	<link rel="stylesheet" href="<?=Url::to('@web/css/wine/recommend/style.css') ?>">
</head>
<body>
<?php
 if(!empty($message)){
	echo $message;
 }else{
?>
		<!--main-->
		<div id="main" style="background: url(<?=Url::to('@web/images/recommend/top.png') ?>) no-repeat center top;background-size: 100%;">
			<div class="download">
				<button class="btn blue large">邀请人:<em><?=$name ?></em>&nbsp; &nbsp;<em>邀请码:</em><em><?=$code ?></em></button>

				
				<div class="state">马上下载双天酒易购APP，好酒送到家！</div>
				<div><a href="https://www.pgyer.com/STJYG" class="down-link">下载双天酒Ⅰ 购APP</a></div>
			</div>
		</div>
<?php
 }
?>
	</body>
</html>