<?php

class AddDailyPointsCommand extends CConsoleCommand {
	public function run() {
		$current_date = date('Y-m-d');
		DailyPointsAdder::addDailyPoints($current_date);
	}
}
