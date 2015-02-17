<?php

class AddImportCommand extends CConsoleCommand  {
	public function run() {
		$import = new Import;
		$import->date = date('Y-m-d');
		$import->save();
	}
}
