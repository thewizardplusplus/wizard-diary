$(document).ready(
	function() {
		var whitelist_clean_form = $('.whitelist-clean-form');
		$('.whitelist-clean-button').click(
			function() {
				WhitelistCleaningDialog.show(
					function() {
						whitelist_clean_form.submit();
					}
				);

				return false;
			}
		);
	}
);
