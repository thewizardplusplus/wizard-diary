<?php

require_once('dropbox-sdk/Dropbox/autoload.php');

class BackupController extends CController {
	const DROPBOX_APP_NAME = 'wizard-diary';

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

		$backups_create_durations = array();
		$backups_from_db = Backup::model()->findAll();
		foreach ($backups_from_db as $backup) {
			$backups_create_durations[$backup->create_time] = round(
				$backup->create_duration,
				Constants::BACKUPS_CREATE_DURATION_ACCURACY
			);
		}

		$backups = array();
		$filenames = scandir($backups_path);
		foreach ($filenames as $filename) {
			$filename = $backups_path . '/' . $filename;
			if (
				is_file($filename)
				and pathinfo($filename, PATHINFO_EXTENSION) == 'xml'
			) {
				$backup = new stdClass();
				$timestamp = filemtime($filename);
				$backup->timestamp = $timestamp;
				$backup->formatted_timestamp =
					'<time>'
						. date('d.m.Y H:i:s', $timestamp)
					. '</time>';
				$file_size = filesize($filename);
				if ($file_size < 1024) {
					$backup->size = $file_size . ' Б';
				} else if ($file_size > 1024 and $file_size < 1024 * 1024) {
					$backup->size =
						round(
							$file_size / 1024,
							Constants::BACKUPS_SIZE_ACCURACY
						)
						. ' КиБ';
				} else if (
					$file_size > 1024 * 1024
					and $file_size < 1024 * 1024 * 1024
				) {
					$backup->size =
						round(
							$file_size / (1024 * 1024),
							Constants::BACKUPS_SIZE_ACCURACY
						)
						. ' МиБ';
				} else {
					$backup->size =
						round(
							$file_size / (1024 * 1024 * 1024),
							Constants::BACKUPS_SIZE_ACCURACY
						)
						. ' ГиБ';
				}
				$create_duration = date('Y-m-d H:i:s', $timestamp);
				if (
					array_key_exists(
						$create_duration,
						$backups_create_durations
					)
				) {
					$backup->create_duration = $backups_create_durations[
						$create_duration
					];
				} else {
					$backup->create_duration = '&mdash;';
				}
				$backup->link = substr(
					realpath($filename),
					strlen($_SERVER['DOCUMENT_ROOT'])
				);

				$backups[] = $backup;
			}
		}

		$data_provider = new CArrayDataProvider(
			$backups,
			array(
				'keyField' => 'timestamp',
				'sort' => array(
					'attributes' => array('timestamp'),
					'defaultOrder' => array('timestamp' => CSort::SORT_DESC)
				)
			)
		);

		$this->render('list', array('data_provider' => $data_provider));
	}

	public function actionCreate() {
		$start_time = microtime(true);

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

		$this->saveFileToDropbox($backup_path);

		$elapsed_time = microtime(true) - $start_time;
		$backup = new Backup();
		$backup->create_time = date('Y-m-d H:i:s', filemtime($backup_path));
		$backup->create_duration = $elapsed_time;
		$backup->save();
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

		return
			"<?xml version = \"1.0\" encoding = \"utf-8\"?>\n"
			. "<diary>\n$days_dump</diary>\n";
	}

	private function saveFileToDropbox($path) {
		$file = fopen($path, 'rb');

		$dropbox_client = new \Dropbox\Client(
			Parameters::getModel()->dropbox_access_token,
			self::DROPBOX_APP_NAME
		);
		$dropbox_client->uploadFile(
			'/' . basename($path),
			\Dropbox\WriteMode::add(),
			$file
		);
	}
}
