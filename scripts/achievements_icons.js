var AchievementsIcons = {};

$(document).ready(
	function() {
		AchievementsIcons = {
			initialize: function() {
				$('.achievement-view .media-object').each(
					function() {
						var self = $(this);
						var icon = new Identicon(self.data('hash'));
						self.attr(
							'src',
							'data:image/png;base64,' + icon.toString()
						);
					}
				);
			},
			afterUpdate: function() {
				AchievementsIcons.initialize();
			}
		};

		AchievementsIcons.initialize();
	}
);
