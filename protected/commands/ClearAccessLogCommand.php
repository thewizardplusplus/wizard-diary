<?php

class ClearAccessLogCommand extends CConsoleCommand {
	public function run() {
		Access::model()->deleteAll(
			'timestamp < SUBDATE(NOW(), INTERVAL '
			. Constants::ACCESS_LOG_LIFETIME_IN_S
			. ' SECOND)'
		);
	}
}
