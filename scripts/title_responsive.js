$(document).ready(function () {
	var MINIMAL_WIDTH_IN_PX = 768;
	var TARGET_CLASS = ".title-responsive";
	var STORED_TITLE_DATA_KEY = "stored-title";

	function ApplyResponsiveTitle() {
		var is_window_small = $(window).width() < MINIMAL_WIDTH_IN_PX;
		$(TARGET_CLASS).each(function () {
			var element = $(this);
			var title_attribute = element.attr("title");
			var stored_title_data = element.data(STORED_TITLE_DATA_KEY);
			if (is_window_small) {
				if (!title_attribute && stored_title_data) {
					element
						.attr("title", stored_title_data)
						.removeData(STORED_TITLE_DATA_KEY);
				}
			} else {
				if (title_attribute && !stored_title_data) {
					element
						.data(STORED_TITLE_DATA_KEY, title_attribute)
						.removeAttr("title");
				}
			}
		});
	}

	$(window).resize(ApplyResponsiveTitle);
	ApplyResponsiveTitle();
});
