<?php

class BackupController extends CController {
	public function filters() {
		return array('accessControl', 'postOnly + create', 'ajaxOnly + create');
	}

	public function accessRules() {
		return array(
			array('allow', 'users' => array('admin')),
			array('deny')
		);
	}

	public function actionList() {
		$backups_path = __DIR__ . Constants::BACKUPS_RELATIVE_PATH;
		BackupController::testBackupDirectory($backups_path);

		$backups = array();
		$filenames = scandir($backups_path);
		foreach ($filenames as $filename) {
			$filename = $backups_path . '/' . $filename;
			if (
				is_file($filename)
				and pathinfo($filename, PATHINFO_EXTENSION) == 'xml'
			) {
				$backup = new stdClass();
				$backup->timestamp = filemtime($filename);
				$backup->formatted_timestamp =
					'<time>'
					. date('d.m.Y H:i:s', $backup->timestamp)
					. '</time>';
				$backup->link = substr(
					realpath($filename),
					strlen($_SERVER['DOCUMENT_ROOT'])
				);

				$backups[] = $backup;
			}
		}

		$data_provider = new CArrayDataProvider($backups, array(
			'keyField' => 'timestamp',
			'sort' => array(
				'attributes' => array('timestamp'),
				'defaultOrder' => array('timestamp' => CSort::SORT_DESC)
			)
		));

		$this->render('list', array('data_provider' => $data_provider));
	}

	public function actionCreate() {
		$backups_path = __DIR__ . Constants::BACKUPS_RELATIVE_PATH;
		BackupController::testBackupDirectory($backups_path);

		$backup_name =
			'database_dump_'
			. date('Y-m-d-H-i-s');
		$backup_path =
			$backups_path
			. '/'
			. $backup_name
			. '.xml';
		$dump = $this->dumpDatabase();
		$result = file_put_contents($backup_path, $dump);
		if (!$result) {
			throw new CException('Не удалось записать бекап на диск.');
		}
	}

	private static function testBackupDirectory($path) {
		if (!file_exists($path)) {
			$result = mkdir($path);
			if (!$result) {
				throw new CException(
					'Не удалось создать директорию для бекапов.'
				);
			}
		}
	}

	private function dumpDatabase() {
		$days = array();
		$points = Point::model()->findAll(array('order' => 'date, `order`'));
		foreach ($points as $point) {
			if (!array_key_exists($point->date, $days)) {
				$days[$point->date] = '';
			}

			$state = $point->state;
			$check = $point->check ? ' check = "true"' : '';
			$text = base64_encode($point->text);

			$days[$point->date] .=
				"\t\t<point state = \"$state\"$check>$text</point>\n";
		}

		$days_dump = '';
		foreach ($days as $date => $points_tags) {
			$days_dump .= "\t<day date = \"$date\">\n$points_tags\t</day>\n";
		}

		$start_date = Parameters::getModel()->start_date;
		return
			"<?xml version = \"1.0\" encoding = \"utf-8\"?>\n"
			. "<diary start-date = \"$start_date\">\n$days_dump</diary>\n";
	}
}
