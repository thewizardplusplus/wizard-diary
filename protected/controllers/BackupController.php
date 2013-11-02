<?php

class BackupController extends CController {
	public function __construct($id, $module = NULL) {
		parent::__construct($id, $module);
		$this->defaultAction = 'list';
	}

	public function filters() {
		return array('accessControl');
	}

	public function accessRules() {
		return array(
			array(
				'allow',
				'actions' => array('list', 'new'),
				'users' => array('admin')
			),
			array(
				'deny',
				'users' => array('*')
			)
		);
	}

	public function actionList() {
		$this->testBackupDirectory();

		$backups_path = __DIR__ . Constants::BACKUPS_RELATIVE_PATH;
		$filenames = scandir($backups_path);
		$backups = array();
		foreach ($filenames as $filename) {
			$filename = $backups_path . '/' . $filename;
			if (is_file($filename) and strtolower(pathinfo($filename,
				PATHINFO_EXTENSION)) == 'zip')
			{
				$backup = new stdClass();
				$backup->timestamp = date('d.m.Y H:i:s', filemtime($filename));
				$backup->size = filesize($filename);
				if ($backup->size > 1024 and $backup->size < 1024 * 1024) {
					$backup->size = round($backup->size / 1024, 2) . ' KB';
				} else if ($backup->size > 1024 * 1024 and $backup->size < 1024
					* 1024 * 1024)
				{
					$backup->size = round($backup->size / 1024 * 1024, 2) .
						' MB';
				} else {
					$backup->size = round($backup->size / 1024 * 1024 * 1024, 2)
						. ' GB';
				}
				$backup->link = substr(realpath($filename), strlen($_SERVER[
					'DOCUMENT_ROOT']));

				$backups[] = $backup;
			}
		}

		$data_provider = new CArrayDataProvider($backups, array(
			'keyField' => 'timestamp',
			'sort' => array(
				'attributes' => array('timestamp'),
				'defaultOrder' => array('timestamp' => CSort::SORT_DESC)
			),
			'pagination' => FALSE
		));

		$log_filename = __DIR__ . '/../runtime/backups.log';
		if (file_exists($log_filename)) {
			$log_text = file_get_contents($log_filename);

			if (!empty($log_text)) {
				$lines = explode("\n", $log_text);
				$lines = array_filter($lines, function($line) {
					return preg_match('/^\d.*/', $line);
				});
				$lines = array_map(function($line) {
					$line = preg_replace('/^(\d{4})\/(0[1-9]|1[0-2])\/(0[1-9]|'
						. '[12]\d|3[01]) (([01]\d|2[0-3]):([0-5]\d):([0-5]' .
						'\d))/', '($3.$2.$1 $4)', $line);
					$line = preg_replace('/\[\w+\]/', '', $line);
					$line = preg_replace('/\s+/', ' ', $line);

					return $line;
				}, $lines);
				$lines = array_reverse($lines);
				$lines = array_slice($lines, 0, Parameters::get()->
					versions_of_backups);

				$log_text = implode("\n", $lines);
			} else {
				$log_text = '';
			}
		} else {
			file_put_contents($log_filename, '');
			$log_text = '';
		}

		$this->render('list', array(
			'data_provider' => $data_provider,
			'log_text' => $log_text
		));
	}

	public function actionNew() {
		$this->testBackupDirectory();

		$start = date_create();

		$result = $this->backup(__DIR__ . '/../..');
		if (!$result) {
			throw new CException('Не удалось создать бекап.');
		}

		$backups_path = __DIR__ . Constants::BACKUPS_RELATIVE_PATH;
		$backups = array_diff(scandir($backups_path), array('.', '..'));
		rsort($backups, SORT_STRING);
		$old_backups = array_slice($backups, Parameters::get()->
			versions_of_backups);
		foreach ($old_backups as $filename) {
			unlink($backups_path . '/' . $filename);
		}
		Yii::log(date_create()->diff($start)->format('Длительность создания'
			. ' последнего бекапа: %I:%S.'), 'info', 'backups');

		$this->redirect(array('backup/list'));
	}

	private function testBackupDirectory() {
		if (!file_exists(__DIR__ . Constants::BACKUPS_RELATIVE_PATH)) {
			$result = mkdir(__DIR__ . Constants::BACKUPS_RELATIVE_PATH);
			if (!$result) {
				throw new CException('Не удалось создать директорию для ' .
					'бекапов.');
			}
		}
	}

	private function backup($path, $context = NULL) {
		if (is_null($context)) {
			$context = new stdClass();
			$context->base_path = $path;
			$context->backup_name = 'backup_' . date('Y-m-d-H-i-s');

			$context->archive = new ZipArchive();
			$result = $context->archive->open(__DIR__ . Constants::
				BACKUPS_RELATIVE_PATH . '/' . $context->backup_name . '.zip',
				ZIPARCHIVE::CREATE);
			if ($result === TRUE) {
				$temporary_filename = sys_get_temp_dir() . '/' . uniqid(rand(),
					TRUE);
				$result = file_put_contents($temporary_filename, $this->
					dumpDatabase());
				if ($result !== FALSE) {
					$result = $context->archive->addFile($temporary_filename,
						$context->backup_name . '/database_dump.sql');
					if ($result) {
						$result = $this->backup($path, $context);
					}
				}

				$context->archive->close();
			}

			return $result;
		} else {
			foreach (array_diff(scandir($path), array('.', '..')) as $file) {
				$full_path = $path . '/' . $file;
				if (realpath($full_path) == realpath(__DIR__ . Constants::
					BACKUPS_RELATIVE_PATH))
				{
					continue;
				}

				if (is_file($full_path)) {
					$result = $context->archive->addFile($path, $context->
						backup_name . str_replace($context->base_path, '',
						$full_path));
				} else if (is_dir($full_path)) {
					$result = $this->backup($full_path, $context);
				}
				if (!$result) {
					return FALSE;
				}
			}

			return TRUE;
		}
	}

	private function dumpDatabase() {
		$connection = Yii::app()->db;

		$tables = array();
		foreach ($connection->createCommand('SHOW TABLES')->queryAll() as $row)
		{
			$tables[] = reset($row);
		}

		$dump = '';
		foreach ($tables as $table) {
			$dump .= "DROP TABLE IF EXISTS `" . $table . "`;\n" . end(reset(
				$connection->createCommand('SHOW CREATE TABLE `' . $table . '`')
				->queryAll())) . ";\n\n";

			$rows = $connection->createCommand('SELECT * FROM `' . $table . '`')
				->queryAll();
			if (!empty($rows)) {
				$dump .= "INSERT INTO `" . $table . "`\nVALUES\n";

				foreach ($rows as $row) {
					$dump .= "\t(" . implode(", ", array_map(function($item)
						use($connection)
					{
						return $connection->quoteValue($item);
					}, $row)) . "),\n";
				}

				$dump = substr($dump, 0, strlen($dump) - 2) . ";\n\n";
			}
		}
		if (!empty($dump)) {
			$dump = substr($dump, 0, strlen($dump) - 1);
		}

		return $dump;
	}
}
