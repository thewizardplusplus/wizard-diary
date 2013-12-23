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
				== 'xml')
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
			'.xml';
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
		$xml =
			"<?xml version = \"1.0\" encoding = \"utf-8\"?>\n"
			. "<diary start-date = \""
			. Parameters::get()->start_date
			. "\">\n";

		$points = Point::model()->findAll();
		$days_xml = '';
		$last_date = '';
		foreach ($points as $point) {
			if ($last_date != '' and $point->date != $last_date) {
				$days_xml .= "\t</day>\n\t<day>\n";
				$last_date = $point->date;
			} elseif ($last_date == '') {
				$last_date = $point->date;
			}

			$days_xml .=
				"\t\t<point state = \""
				. $point->state .
				"\"" . ($point->check ? " check = \"true\"" : "") . ">" .
				base64_encode($point->text) .
				"</point>\n";
		}
		if (!empty($days_xml)) {
			$xml .= "\t<day>\n" . $days_xml . "\t</day>\n";
		}

		$xml .= '</diary>' . "\n";
		return $xml;
	}
}
