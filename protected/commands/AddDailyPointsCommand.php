<?php

class AddDailyPointsCommand extends CConsoleCommand {
	public function run($args) {
		$current_date = date('Y-m-d');
		DailyPointsAdder::addDailyPoints($current_date);
	}
}
