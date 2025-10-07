var FinishingButton = {};

$(document).ready(function () {
  var finishing_button = $(".finishing-button");
  var finishing_url = finishing_button.data("finishing-url");
  var stats_url = finishing_button.data("stats-url");
  var processing_animation_image = $("img", finishing_button);
  var finishing_icon = $("span", finishing_button);
  var point_list = $("#point-list");

  finishing_button.click(function () {
    FinishingDialog.show(function () {
      finishing_button.attr("disabled", "disabled");

      processing_animation_image.show();
      finishing_icon.hide();

      point_list.yiiGridView("update", {
        type: "POST",
        url: finishing_url,
        data: CSRF_TOKEN,
        success: function (data) {
          point_list.yiiGridView("update", {
            url: location.pathname + location.search + location.hash,
          });

          processing_animation_image.hide();
          finishing_icon.show();
        },
      });

      FinishingDialog.hide();
    });
  });

  FinishingButton = {
    update: function () {
      $.get(
        stats_url,
        function (data) {
          finishing_button.attr(
            "disabled",
            data.completed == "1" ? "disabled" : null
          );
        },
        "json"
      ).fail(AjaxErrorDialog.handler);
    },
  };
});
