$(document).ready(
	function() {
		var RequestAnimationFrame =
			requestAnimationFrame
			|| mozRequestAnimationFrame
			|| webkitRequestAnimationFrame
			|| function(callback) {
				setTimeout(callback, 1000 / 60);
			};
		var CorrectUrl = function() {
			var point_page = $.url(location.href).param('Point_page');
			if (
				typeof point_page == 'undefined'
				|| !/^[1-9]\d*$/.test(point_page)
				|| parseInt(point_page) < 1
				|| parseInt(point_page) > NUMBER_OF_PAGES
			) {
				RequestAnimationFrame(CorrectUrl);
				$('.page-controller .next a').first().click();
			}
		};

		CorrectUrl();
	}
);
