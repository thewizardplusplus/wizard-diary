var ImportList = {};

$(document).ready(
	function() {
		ImportList = {
			import: function(link) {
				var import_url = $(link).attr('href');
				var import_date = $.url(import_url).param('date');
				ImportDialog.show(
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
