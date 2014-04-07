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
			if (location.search == '') {
				RequestAnimationFrame(CorrectUrl);
			}

			$('.page-controller .next a').first().click();
		};

		CorrectUrl();
	}
);
