<?php

require_once('dropbox-sdk/Dropbox/autoload.php');

class BackupController extends CController {
	/* v5:
	 *     - remove checks from points and daily points;
	 *     - remove imports;
	 */
	const BACKUP_VERSION = 5;

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
		BackupController::testBackupDirectory($backups_path);

		$backups_create_durations = array();
		$backups_from_db = Backup::model()->findAll();
		foreach ($backups_from_db as $backup) {
			$backups_create_durations[$backup->create_time] = array(
				'create_duration' => $backup->create_duration,
				'save_duration' => $backup->save_duration,
			);
		}

		$backups = array();
		$filenames = scandir($backups_path);
		foreach ($filenames as $filename) {
			$filename = $backups_path . '/' . $filename;
			if (
				!is_file($filename)
				|| pathinfo($filename, PATHINFO_EXTENSION) != 'xml'
			) {
				continue;
			}

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

			$backup->create_duration = '&mdash;';
			$backup->save_duration = '&mdash;';
			$create_duration_index = date('Y-m-d H:i:s', $timestamp);
			if (
				array_key_exists(
					$create_duration_index,
					$backups_create_durations
				)
			) {
				$durations = $backups_create_durations[
					$create_duration_index
				];
				if ($durations['create_duration'] != 0) {
					$backup->create_duration = round(
						$durations['create_duration'],
						Constants::BACKUPS_CREATE_DURATION_ACCURACY
					);
				}
				if ($durations['save_duration'] != 0) {
					$backup->save_duration = round(
						$durations['save_duration'],
						Constants::BACKUPS_CREATE_DURATION_ACCURACY
					);
				}
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

		$this->render('list', array('data_provider' => $data_provider));
	}

	public function actionCreate() {
		$start_time = microtime(true);

		$backup_path = __DIR__ . Constants::BACKUPS_RELATIVE_PATH;
		BackupController::testBackupDirectory($backup_path);

		$backup_name = 'database_dump_' . date('Y-m-d-H-i-s');
		$backup_path .= '/' . $backup_name . '.xml';
		$dump = $this->dumpDatabase();
		$result = file_put_contents($backup_path, $dump);
		if (!$result) {
			throw new CException('Не удалось записать бекап на диск.');
		}

		$create_time = date('Y-m-d H:i:s', filemtime($backup_path));
		$data = array(
			'backup_path' => $backup_path,
			'create_time' => $create_time
		);
		echo json_encode($data);

		$elapsed_time = microtime(true) - $start_time;
		$backup = new Backup();
		$backup->create_time = $create_time;
		$backup->create_duration = $elapsed_time;
		$backup->save();
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

		return
			"<?xml version = \"1.0\" encoding = \"utf-8\"?>\n"
			. "<diary version = \"" . self::BACKUP_VERSION . "\">\n"
				. "\t<days>\n"
					. "$days_dump"
				. "\t</days>\n"
				. "\t<daily-points>\n"
					. "$daily_points_dump"
				. "\t</daily-points>\n"
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
}
