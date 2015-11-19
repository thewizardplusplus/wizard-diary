var AchievementsIcons = {};

$(document).ready(
	function() {
		AchievementsIcons = {
			initialize: function() {
				jdenticon();
			},
			afterUpdate: function() {
				AchievementsIcons.initialize();
			}
		};

		AchievementsIcons.initialize();
	}
);
