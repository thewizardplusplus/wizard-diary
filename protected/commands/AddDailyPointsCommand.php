<?php

class AddDailyPointsCommand extends CConsoleCommand {
	public function run() {
		DailyPointsAdder::addDailyPoints();
	}
}
