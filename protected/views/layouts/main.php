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
			<div id = "mainmenu">
				<?php $this->widget('zii.widgets.CMenu',array(
					'items'=>array(
						array('label' => 'Главная', 'url' => array(
							'/point/index'), 'visible' => !Yii::app()->user->
							isGuest),
						array('label' => 'Добавить', 'url' => array(
							'/point/create'), 'visible' => !Yii::app()->user->
							isGuest),
						array('label' => 'Вход', 'url' => array('/site/login'),
							'visible' => Yii::app()->user->isGuest),
						array('label' => 'Выход', 'url' => array(
							'/site/logout'), 'visible' => !Yii::app()->user->
							isGuest)
					),
				)); ?>
			</div>
			<?php echo $content; ?>
			<div class = "clear"></div>
			<div id = "footer">
				&copy; <?php echo date('Y'); ?>, thewizardplusplus.<br />
				<?php echo Yii::powered(); ?>
			</div>
		</div>
	</body>
</html>
