var ImportList = {};

$(document).ready(
	function() {
		ImportList = {
			import: function(link) {
				var import_url = $(link).attr('href');
				var wrapped_import_url = $.url(import_url);
				var import_date = wrapped_import_url.param('date');
				var import_my_date = wrapped_import_url.param('my-date');
				ImportDialog.show(
					import_my_date,
					import_date,
					function() {
						window.location = import_url;
					}
				);

				return false;
			}
		};
	}
);
