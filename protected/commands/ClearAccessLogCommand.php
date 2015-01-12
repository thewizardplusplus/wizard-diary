<?php

class ClearAccessLogCommand extends CConsoleCommand {
	public function run() {
		Access::model()->deleteAll(
			'timestamp < SUBDATE(NOW(), INTERVAL '
			. Parameters::getModel()->access_log_lifetime_in_s
			. ' SECOND)'
		);
	}
}
