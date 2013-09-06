<?php /* @var $this Controller */ ?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset = "utf-8" />
		<title><?php echo CHtml::encode($this->pageTitle); ?></title>
		<link rel = "stylesheet" href = "<?php echo Yii::app()->request->
			baseUrl; ?>/bootstrap/css/bootstrap.min.css" />
		<link rel = "stylesheet" href = "<?php echo Yii::app()->request->
			baseUrl; ?>/jquery-ui/css/theme/jquery-ui.min.css" />
		<link rel = "stylesheet" href = "<?php echo Yii::app()->request->
			baseUrl; ?>/jQueryFormStyler/jquery.formstyler.css" />
		<link rel = "stylesheet" href = "<?php echo Yii::app()->request->
			baseUrl; ?>/css/diary.css" />
		<script src = "<?php echo Yii::app()->request->baseUrl;
			?>/bootstrap/js/bootstrap.min.js"></script>
		<script src = "<?php echo Yii::app()->request->baseUrl;
			?>/jquery-ui/js/jquery-ui.min.js"></script>
		<script src = "<?php echo Yii::app()->request->baseUrl;
			?>/jQueryFormStyler/jquery.formstyler.min.js"></script>
		<script src = "<?php echo Yii::app()->request->baseUrl; ?>/js/diary.js">
			</script>
	</head>
	<body>
		<section class = "container panel panel-default">
			<header class = "page-header">
				<h1>
					<span class = "glyphicon glyphicon-list-alt"></span> <?php
						echo CHtml::encode(Yii::app()->name); ?>
				</h1>
			</header>

			<?php if (!Yii::app()->user->isGuest) { ?>
			<nav>
				<?php $this->widget('zii.widgets.CMenu',array(
					'items'=>array(
						array('label' => 'Главная', 'url' => array(
							'point/list')),
						array('label' => 'Параметры', 'url' => array(
							'parameters/update')),
						array('label' => 'Выход', 'url' => array('site/logout'))
					),
					'htmlOptions' => array('class' => 'nav nav-pills')
				)); ?>
			</nav>
			<?php } ?>

			<?php echo $content; ?>

			<footer>
				<hr />
				&copy; <?php echo date('Y'); ?>, thewizardplusplus.<br />
				<?php echo Yii::powered(); ?>
			</footer>
		</section>
	</body>
</html>
