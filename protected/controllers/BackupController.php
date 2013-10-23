<?php

class BackupController extends CController {
	const BACKUPS_RELATIVE_PATH = '/../backups';

	public function __construct($id, $module = NULL) {
		parent::__construct($id, $module);
		$this->defaultAction = 'list';
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
				$size = filesize($filename);
				if ($size > 1024 and $size < 1024 * 1024) {
					$size /= 1024;
					$size = round($size, 2);
					$size .= ' KB';
				} else if ($size > 1024 * 1024 and $size < 1024 * 1024 * 1024) {
					$size /= 1024 * 1024;
					$size = round($size, 2);
					$size .= ' MB';
				} else {
					$size /= 1024 * 1024 * 1024;
					$size = round($size, 2);
					$size .= ' GB';
				}

				$backups[] = array(
					'timestamp' => date('d.m.Y H:i:s', filemtime($filename)),
					'size' => $size,
					'link' => 'http://' . $_SERVER['HTTP_HOST'] . str_replace(
						$_SERVER['DOCUMENT_ROOT'], '', realpath($filename))
				);
			}
		}

		$data_provider = new CArrayDataProvider($backups, array(
			'keyField' => 'timestamp',
			'pagination' => FALSE
		));

		$this->render('list', array('data_provider' => $data_provider));
	}

	public function actionNew() {
		$result = $this->backup(__DIR__ . '/../..');
		if (!$result) {
			throw new CException('Не удалось создать бекап.');
		}

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
				$result = $this->backup($source_directory, $context);
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
}
