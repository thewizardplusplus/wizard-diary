<?php

class ClearAccessLogCommand extends CConsoleCommand {
	public function run($args) {
		$access_log_lifetime = Parameters::getModel()->access_log_lifetime_in_s;
		if (intval($access_log_lifetime) === 0) {
			return;
		}

		Access::model()->deleteAll(
			'timestamp < SUBDATE(NOW(), INTERVAL '
			. $access_log_lifetime
			. ' SECOND)'
		);
	}
}
