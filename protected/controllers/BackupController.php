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
				'actions' => array('list', 'create'),
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
			if (is_file($filename) and pathinfo($filename, PATHINFO_EXTENSION)
				== 'sql')
			{
				$backup = new stdClass;
				$backup->timestamp = date('d.m.Y H:i:s', filemtime($filename));
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
			)
		));

		$this->render('list', array('data_provider' => $data_provider));
	}

	public function actionCreate() {
		$this->testBackupDirectory();

		$dump = $this->dumpDatabase();
		if (empty($dump)) {
			throw new CException('Не удалось создать бекап.');
		}

		$backups_path = __DIR__ . Constants::BACKUPS_RELATIVE_PATH;
		$dump_name = $backups_path . '/database_dump_' . date('Y-m-d-H-i-s') .
			'.sql';
		$result = file_put_contents($dump_name, $dump);
		if (!$result) {
			throw new CException('Не удалось создать бекап.');
		}

		if (!isset($_POST['ajax'])) {
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl']
				: array('list'));
		}
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

	private function dumpDatabase() {
		$connection = Yii::app()->db;

		$tables = array();
		foreach ($connection->createCommand('SHOW TABLES')->queryAll() as $row)
		{
			$table = reset($row);
			$table_prefix = Yii::app()->db->tablePrefix;
			if (substr($table, 0, strlen($table_prefix)) == $table_prefix) {
				$tables[] = $table;
			}
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
