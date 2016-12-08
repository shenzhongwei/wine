<?php
use yii\helpers\Url;

/**
 * @var string $message
 * @var string $name
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta charset="utf-8">
		<meta content="width=device-width, initial-scale=1.0, user-scalable=no" name="viewport">
		<!-- 针对手持设备优化，主要是针对一些老的不识别viewport的浏览器，比如黑莓 -->
		<meta name="HandheldFriendly" content="True">
		<meta content="yes" name="apple-mobile-web-app-capable">
		<meta content="yes" name="apple-touch-fullscreen">
		<meta content="black" name="apple-mobile-web-app-status-bar-style">
		<meta content="telephone=no" name="format-detection">
		<meta content="webkit" name="renderer">		
		<script type="application/javascript" src="<?=Url::to('@web/js/share/flexible_css.debug.js') ?>"></script>
		<script type="application/javascript" src="<?=Url::to('@web/js/share/flexible.debug.js') ?>"></script>
		<title>双天酒易购</title>
		<link rel="stylesheet" href="<?=Url::to('@web/css/share/style.css') ?>">
	</head>
	<body>
	<?php
	if(!empty($message)){
		echo '<span style="font-size: 30px">'.$message.'</span>';
	}else {
		?>
		<!--main-->
		<div id="main">
			<div class="browser">
				<div class="pic"><img src="<?= Url::to('@web/images/share/share.jpg') ?>" alt="双天酒易购，好酒好生活"></div>
				<?php
				if(!empty($name)) {
					?>
					<div class="recom">
						<span>推荐人手机：<em><?=$name ?></em></span>
					</div>
					<?php
				}
				?>
				<div class="qr_wrapper">
					<img src="<?= Url::to('@web/images/share/qr.png') ?>" alt="下载51酒易购客户端">
					<br/>
					<button class="btn download">下载APP客户端</button>
				</div>
			</div>
		</div>		<?php
	}
	?>
	</body>
</html>