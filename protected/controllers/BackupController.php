<?php

require_once(__DIR__ . '/../../dropbox-sdk/Dropbox/autoload.php');
require_once(__DIR__ . '/../../php-diff/Diff.php');
require_once(__DIR__ . '/../../php-diff/Diff/Renderer/Text/Unified.php');

class BackupController extends CController {
	/* v6:
	 *     - add spellings;
	 *
	 * v5:
	 *     - remove checks from points and daily points;
	 *     - remove imports;
	 */
	const BACKUP_VERSION = 6;

	public function filters() {
		return array(
			'accessControl',
			'postOnly + create, save',
			'ajaxOnly + create, save'
		);
	}

	public function accessRules() {
		return array(array('allow', 'users' => array('admin')), array('deny'));
	}

	public function actionList() {
		$backups_path = __DIR__ . Constants::BACKUPS_RELATIVE_PATH;
		$this->testBackupDirectory($backups_path);

		$backups_db_data = array();
		$backups_from_db = Backup::model()->findAll();
		foreach ($backups_from_db as $backup) {
			$backups_db_data[$backup->create_time] = array(
				'create_duration' => $backup->create_duration,
				'save_duration' => $backup->save_duration,
				'has_difference' => $backup->has_difference
			);
		}

		$backups = array();
		$filenames = $this->getBackupList($backups_path);
		$number_of_filenames = count($filenames);
		for ($i = 0; $i < $number_of_filenames; $i++) {
			$backup = new stdClass();

			$filename = $filenames[$i];
			$backup->filename = $this->getBackupDate($filename);
			$backup->previous_filename = null;
			if ($i < $number_of_filenames - 1) {
				$backup->previous_filename = $this->getBackupDate(
					$filenames[$i + 1]
				);
			}

			$filename = $backups_path . '/' . $filename;
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

			$backup->create_duration = '&mdash;';
			$backup->save_duration = '&mdash;';
			$backup->has_difference = false;
			$create_duration_index = date('Y-m-d H:i:s', $timestamp);
			if (
				array_key_exists(
					$create_duration_index,
					$backups_db_data
				)
			) {
				$backup_data = $backups_db_data[$create_duration_index];
				if ($backup_data['create_duration'] != 0) {
					$backup->create_duration = round(
						$backup_data['create_duration'],
						Constants::BACKUPS_CREATE_DURATION_ACCURACY
					);
				}
				if ($backup_data['save_duration'] != 0) {
					$backup->save_duration = round(
						$backup_data['save_duration'],
						Constants::BACKUPS_CREATE_DURATION_ACCURACY
					);
				}
				$backup->has_difference = $backup_data['has_difference'];
			}

			$backup->link = substr(
				realpath($filename),
				strlen($_SERVER['DOCUMENT_ROOT'])
			);

			$backups[] = $backup;
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

		$last_backup_date = null;
		$has_current_difference = false;
		if ($number_of_filenames > 0) {
			$last_backup_date = $this->getBackupDate($filenames[0]);

			$difference = $this->getBackupsDiff($last_backup_date, null);
			$has_current_difference = strlen($difference) > 0;
		}

		$this->render(
			'list',
			array(
				'data_provider' => $data_provider,
				'last_backup_date' => $last_backup_date,
				'has_current_difference' => $has_current_difference
			)
		);
	}

	public function actionCreate() {
		$start_time = microtime(true);

		$base_backup_path = __DIR__ . Constants::BACKUPS_RELATIVE_PATH;
		$this->testBackupDirectory($base_backup_path);

		$backup_name = 'database_dump_' . date('Y-m-d-H-i-s');
		$backup_path = $base_backup_path . '/' . $backup_name . '.xml';
		$dump = $this->dumpDatabase();
		$result = file_put_contents($backup_path, $dump);
		if (!$result) {
			throw new CException('Не удалось записать бекап на диск.');
		}

		$backup = new Backup();
		$create_time = date('Y-m-d H:i:s', filemtime($backup_path));
		$backup->create_time = $create_time;

		$elapsed_time = microtime(true) - $start_time;
		$backup->create_duration = $elapsed_time;

		$backups = $this->getBackupList($base_backup_path);
		$file = $this->getBackupDate($backups[0]);
		$previous_file = $this->getBackupDate($backups[1]);
		$difference = $this->getBackupsDiff($previous_file, $file);
		$backup->has_difference = strlen($difference) > 0;

		$backup->save();

		echo json_encode(
			array(
				'backup_path' => $backup_path,
				'create_time' => $create_time
			)
		);
	}

	public function actionSave() {
		$start_time = microtime(true);

		if (!isset($_POST['authorization_code'])) {
			throw new CException('Не передан авторизационный код.');
		}
		if (!isset($_POST['backup_path'])) {
			throw new CException('Не передан путь к бекапу.');
		}

		$this->saveFileToDropbox(
			$_POST['authorization_code'],
			$_POST['backup_path']
		);

		if (isset($_POST['create_time'])) {
			$elapsed_time = microtime(true) - $start_time;
			$backup = Backup::model()->findByAttributes(
				array('create_time' => $_POST['create_time'])
			);
			$backup->save_duration = $elapsed_time;
			$backup->save();
		}
	}

	public function actionRedirect() {
		if (!isset($_GET['state'])) {
			throw new CException("Не передан CSRF токен.");
		}
		if ($_GET['state'] != Yii::app()->request->csrfToken) {
			throw new CException("Неверный CSRF токен.");
		}

		echo '<!DOCTYPE html>';
		echo '<meta charset = "utf-8" />';
		echo '<title>Backup redirect page</title>';
		echo '<script>';

		echo 'if (window.opener) {';
		if (isset($_GET['code'])) {
			echo
				'window.opener.Backup.create('
					. "'" . CHtml::encode($_GET['code']) . "'"
				. ');';
		} else if (
			isset($_GET['error'])
			and $_GET['error'] != 'access_denied'
		) {
			echo
				'window.opener.Backup.error('
					. "'" . (isset($_GET['error_description'])
						? CHtml::encode($_GET['error_description'])
						: '') . "'"
				. ');';
		}
		echo '}';
		echo 'close();';

		echo '</script>';
	}

	public function actionDiff($file, $previous_file) {
		$diff_representation = $this->getBackupsDiff($previous_file, $file);
		$previous_file_timestamp = $this->formatBackupTimestamp($previous_file);
		$file_timestamp = $this->formatBackupTimestamp($file);

		$this->render(
			'diff',
			array(
				'diff_representation' => $diff_representation,
				'previous_file_timestamp' => $previous_file_timestamp,
				'file_timestamp' => $file_timestamp
			)
		);
	}

	public function actionCurrentDiff($file) {
		$diff_representation = $this->getBackupsDiff($file, null);
		$previous_file_timestamp = $this->formatBackupTimestamp($file);

		$this->render(
			'diff',
			array(
				'diff_representation' => $diff_representation,
				'previous_file_timestamp' => $previous_file_timestamp,
				'file_timestamp' => null
			)
		);
	}

	private function getBackupList($backups_path) {
		$backups = array();
		$filenames = scandir($backups_path);
		foreach ($filenames as $filename) {
			if (
				!is_file($backups_path . '/' . $filename)
				|| pathinfo($filename, PATHINFO_EXTENSION) != 'xml'
			) {
				continue;
			}

			$backups[] = $filename;
		}

		rsort($backups);
		return $backups;
	}

	private function getBackupDate($backup_filename) {
		$backup_filename = preg_replace(
			'/^database_dump_/',
			'',
			$backup_filename
		);
		$backup_filename = preg_replace('/\.xml$/', '', $backup_filename);
		return $backup_filename;
	}

	private function testBackupDirectory($path) {
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
			$daily = $point->daily ? ' daily = "true"' : '';
			$text =
				!empty($point->text)
					? '<![CDATA['
						. str_replace(']]>', ']]]><![CDATA[]>', $point->text)
						. ']]>'
					: '';

			$days[$point->date] .=
				"\t\t\t<point state = \"$state\"$daily>$text</point>\n";
		}

		$days_dump = '';
		foreach ($days as $date => $points_tags) {
			$days_dump .=
				"\t\t<day date = \"$date\">\n"
					. "$points_tags"
				. "\t\t</day>\n";
		}

		$daily_points_dump = '';
		$daily_points = DailyPoint::model()->findAll(
			array('order' => '`order`')
		);
		foreach ($daily_points as $daily_point) {
			$text =
				!empty($daily_point->text)
					? '<![CDATA['
						. str_replace(
							']]>',
							']]]><![CDATA[]>',
							$daily_point->text
						)
						. ']]>'
					: '';

			$daily_points_dump .= "\t\t<daily-point>$text</daily-point>\n";
		}

		$spellings_dump = '';
		$spellings = Spelling::model()->findAll(array('order' => 'word'));
		foreach ($spellings as $spelling) {
			$word = $spelling->word;
			$spellings_dump .= "\t\t<spelling>$word</spelling>\n";
		}

		return
			"<?xml version = \"1.0\" encoding = \"utf-8\"?>\n"
			. "<diary version = \"" . self::BACKUP_VERSION . "\">\n"
				. "\t<days>\n"
					. "$days_dump"
				. "\t</days>\n"
				. "\t<daily-points>\n"
					. "$daily_points_dump"
				. "\t</daily-points>\n"
				. "\t<spellings>\n"
					. "$spellings_dump"
				. "\t</spellings>\n"
			. "</diary>\n";
	}

	private function saveFileToDropbox($authorization_code, $filename) {
		$curl = curl_init('https://api.dropbox.com/1/oauth2/token');
		if ($curl === false) {
			throw new CException('Не удалось инициализировать cURL.');
		}

		$protocol =
			((!empty($_SERVER['HTTPS']) and $_SERVER['HTTPS'] != 'off')
			or $_SERVER['SERVER_PORT'] == 443)
				? 'https://'
				: 'http://';
		$redirect_uri =
			$protocol
			. $_SERVER['HTTP_HOST']
			. Constants::DROPBOX_REDIRECT_URL;

		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt(
			$curl,
			CURLOPT_POSTFIELDS,
			array(
				'grant_type' => 'authorization_code',
				'code' => $authorization_code,
				'client_id' => Constants::DROPBOX_APP_KEY,
				'client_secret' => Constants::DROPBOX_APP_SECRET,
				'redirect_uri' => $redirect_uri
			)
		);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$answer = curl_exec($curl);
		if ($answer === false) {
			throw new CException('Не удалось получить Dropbox access token.');
		}

		$access_data = json_decode($answer, true);
		if (
			empty($access_data)
			or !is_array($access_data)
			or !array_key_exists('access_token', $access_data)
		) {
			throw new CException(
				'Не удалось декодировать Dropbox access token.'
			);
		}

		$file = fopen($filename, 'rb');
		$dropbox_client = new \Dropbox\Client(
			$access_data['access_token'],
			Constants::DROPBOX_APP_NAME
		);
		$dropbox_client->uploadFile(
			'/' . basename($filename),
			\Dropbox\WriteMode::add(),
			$file
		);
	}

	private function getBackupsDiff($previous_file, $file) {
		$backups_path = __DIR__ . Constants::BACKUPS_RELATIVE_PATH;
		$previous_filename =
			$backups_path
			. '/'
			. $this->makeBackupFilename($previous_file);
		$previous_filename_lines = file(
			$previous_filename,
			FILE_IGNORE_NEW_LINES
		);

		if (!is_null($file)) {
			$filename = $backups_path . '/' . $this->makeBackupFilename($file);
			$filename_lines = file($filename, FILE_IGNORE_NEW_LINES);
		} else {
			$current_dump = trim($this->dumpDatabase());
			$filename_lines = explode("\n", $current_dump);
		}

		$diff = new Diff($previous_filename_lines, $filename_lines);

		$diff_renderer = new Diff_Renderer_Text_Unified;
		$diff_representation = $diff->Render($diff_renderer);

		return $diff_representation;
	}

	private function makeBackupFilename($backup_date) {
		return sprintf('database_dump_%s.xml', $backup_date);
	}

	private function formatBackupTimestamp($timestamp) {
		$timestamp_parts = explode('-', $timestamp);
		$date = DateFormatter::formatDate(
			implode('-', array_slice($timestamp_parts, 0, 3))
		);
		$time = implode(':', array_slice($timestamp_parts, 3));
		return sprintf('%s %s', $date, $time);
	}
}
