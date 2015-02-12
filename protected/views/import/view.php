<?php

/* @var $this ImportController */
/* @var $model Import */

$this->pageTitle = Yii::app()->name . ' - ' . $model->date;

$this->renderPartial('_view', array('data' => $model));
