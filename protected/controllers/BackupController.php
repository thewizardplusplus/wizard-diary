<?php

class BackupController extends CController {
	const BACKUPS_RELATIVE_PATH = '/../backups';

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
		$backups_path = __DIR__ . BackupController::BACKUPS_RELATIVE_PATH;
		$filenames = scandir($backups_path);
		$backups = array();
		foreach ($filenames as $filename) {
			$filename = $backups_path . '/' . $filename;
			if (is_file($filename) and strtolower(pathinfo($filename,
				PATHINFO_EXTENSION)) == 'zip')
			{
				$backup = new stdClass;
				$backup->timestamp = date('d.m.Y H:i:s', filemtime($filename));
				$backup->size = filesize($filename);
				if ($backup->size > 1024 and $backup->size < 1024 * 1024) {
					$backup->size /= 1024;
					$backup->size = round($backup->size, 2);
					$backup->size .= ' KB';
				} else if ($backup->size > 1024 * 1024 and $backup->size < 1024
					* 1024 * 1024)
				{
					$backup->size /= 1024 * 1024;
					$backup->size = round($backup->size, 2);
					$backup->size .= ' MB';
				} else {
					$backup->size /= 1024 * 1024 * 1024;
					$backup->size = round($backup->size, 2);
					$backup->size .= ' GB';
				}
				$backup->link = 'http://' . $_SERVER['HTTP_HOST'] . str_replace(
					$_SERVER['DOCUMENT_ROOT'], '', realpath($filename));
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

		$log = file_get_contents(__DIR__ . '/../runtime/backups.log');
		$log_lines = explode("\n", $log);
		$log_lines = array_filter($log_lines, function($log_line) {
			return preg_match('/^\d.*/', $log_line);
		});
		$log_lines = array_map(function($log_line) {
			$log_line = preg_replace('/^(\d{4})\/(\d{2})\/(\d{2}) (\d{2}:\d{2}:'
				. '\d{2})/', '($3.$2.$1 $4)', $log_line);
			$log_line = preg_replace('/\[\w+\]/', '', $log_line);
			$log_line = preg_replace('/\s+/', ' ', $log_line);
			return $log_line;
		}, $log_lines);
		$log_lines = array_reverse($log_lines);
		$log_lines = array_slice($log_lines, 0, Parameters::get()->
			versions_of_backups);
		$log = implode("\n", $log_lines);

		$this->render('list', array(
			'data_provider' => $data_provider,
			'log' => $log
		));
	}

	public function actionNew() {
		$start = date_create();
		$result = $this->backup(__DIR__ . '/../..');
		if (!$result) {
			throw new CException('Не удалось создать бекап.');
		}

		$backups_path = __DIR__ . BackupController::BACKUPS_RELATIVE_PATH;
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

	private function backup($source_directory, $context = array()) {
		if (empty($context)) {
			$context['base_path'] = $source_directory;
			$context['backup_name'] = 'backup_' . date('Y-m-d-H-i-s');

			$context["archive"] = new ZipArchive();
			$result = $context["archive"]->open(__DIR__ . BackupController::
				BACKUPS_RELATIVE_PATH . '/' . $context['backup_name'] . '.zip',
				ZIPARCHIVE::CREATE);
			if ($result === TRUE) {
				$temporary_filename = sys_get_temp_dir() . '/' . uniqid(rand(),
					TRUE);
				$result = file_put_contents($temporary_filename, $this->
					dumpDatabase());
				if ($result !== FALSE) {
					$result = $context['archive']->addFile($temporary_filename,
						$context['backup_name'] . '/database_dump.sql');
					//unlink($temporary_filename));

					$new_result = $this->backup($source_directory, $context);
					if ($result) {
						$result = $new_result;
					}
				}

				$context['archive']->close();
				return $result;
			} else {
				return FALSE;
			}
		} else {
			$global_result = TRUE;
			foreach (array_diff(scandir($source_directory), array('.', '..')) as
				 $file)
			{
				$path = $source_directory . '/' . $file;
				if (realpath($path) == realpath(__DIR__ . BackupController::
					BACKUPS_RELATIVE_PATH))
				{
					continue;
				}

				$result = FALSE;
				if (is_file($path)) {
					$result = $context['archive']->addFile($path, $context[
						'backup_name'] . str_replace($context['base_path'], '',
						$path));
				} else if (is_dir($path)) {
					$result = $this->backup($path, $context);
				}

				if (!$result) {
					$global_result = FALSE;
				}
			}

			return $global_result;
		}
	}

	private function dumpDatabase() {
		$sql_dump = '';
		$database = Yii::app()->db;

		$command = $database->createCommand('SHOW TABLES;');
		$rows = $command->queryAll();
		$tables = array();
		foreach ($rows as $row) {
			$tables[] = reset($row);
		}

		foreach ($tables as $table) {
			$command = $database->createCommand('SHOW CREATE TABLE `' . $table .
			'`;');
			$rows = $command->queryAll();
			$sql_dump .= "DROP TABLE IF EXISTS `" . $table . "`;\n" . end(reset(
				$rows)) . ";\n\n";

			$command = $database->createCommand('SELECT * FROM `' . $table .
			'`;');
			$rows = $command->queryAll();
			if (!empty($rows)) {
				$sql_dump .= "INSERT INTO `" . $table . "` VALUES\n";
				foreach ($rows as $row) {
					$sql_dump .= "\t('" . implode("', '", str_replace(array(
						"\0", "\n", "\r", "\\", "'", "\"", "\x1a"), array("\\0",
						"\\n", "\\r", "\\\\", "\\'", "\\\"", "\Z"), $row)) .
						"'),\n";
				}
				$sql_dump = substr($sql_dump, 0, strlen($sql_dump) - 2) .
					";\n\n";
			}
		}

		if (!empty($sql_dump)) {
			$sql_dump = substr($sql_dump, 0, strlen($sql_dump) - 1);
		}

		return $sql_dump;
	}
}
