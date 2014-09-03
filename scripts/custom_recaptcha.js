var RecaptchaOptions = {
	theme: 'custom',
	lang: 'ru',
	custom_theme_widget: 'recaptcha_widget'
};

$(document).ready(
	function() {
		var RequestAnimationFrame =
			requestAnimationFrame
				|| mozRequestAnimationFrame
				|| webkitRequestAnimationFrame
				|| function(callback) {
				setTimeout(callback, 1000 / 60);
			};
		var CorrectImageStyle = function() {
			RequestAnimationFrame(CorrectImageStyle);

			var image_container = $('#recaptcha_image');
			image_container.width('auto');
			image_container.height('auto');

			var image = $('img', image_container);
			if (image.length && !image.hasClass('img-thumbnail')) {
				image.addClass('img-thumbnail');
			}
		};

		CorrectImageStyle();
		$('.recaptcha-refresh').click(Recaptcha.reload);
	}
);
