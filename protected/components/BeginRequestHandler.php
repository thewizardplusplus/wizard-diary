<?php

class BeginRequestHandler {
	public static function handle() {
		AccessController::accessProcess();
		AccessCode::cleanIfNeed();
		SessionGuard::check();
	}
}
