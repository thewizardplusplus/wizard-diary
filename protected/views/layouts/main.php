<?php /* @var $this Controller */ ?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset = "utf-8" />
		<title><?php echo CHtml::encode($this->pageTitle); ?></title>
		<link rel = "stylesheet" type = "text/css" href = "<?php echo Yii::app()
			->request->baseUrl; ?>/css/screen.css" />
		<link rel = "stylesheet" type = "text/css" href = "<?php echo Yii::app()
			->request->baseUrl; ?>/css/main.css" />
		<link rel = "stylesheet" type = "text/css" href = "<?php echo Yii::app()
			->request->baseUrl; ?>/css/form.css" />
	</head>
	<body>
		<div id = "page" class = "container">
			<div id = "header">
				<div id = "logo">
					<?php echo CHtml::encode(Yii::app()->name); ?>
				</div>
			</div>

			<?php if (!Yii::app()->user->isGuest) { ?>
			<div id = "mainmenu">
				<?php $this->widget('zii.widgets.CMenu',array(
					'items'=>array(
						array('label' => 'Главная', 'url' => array(
							'point/list')),
						array('label' => 'Параметры', 'url' => array(
							'parameters/update')),
						array('label' => 'Выход', 'url' => array('site/logout'))
					),
				)); ?>
			</div>
			<?php } ?>

			<div id = "content">
				<?php echo $content; ?>
			</div>

			<div id = "footer">
				&copy; <?php echo date('Y'); ?>, thewizardplusplus.<br />
				<?php echo Yii::powered(); ?>
			</div>
		</div>
	</body>
</html>
