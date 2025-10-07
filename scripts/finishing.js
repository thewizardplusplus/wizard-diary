var FinishingButton = {};

$(document).ready(function () {
  var finishing_button = $(".finishing-button");
  var finishing_url = finishing_button.data("finishing-url");
  var stats_url = finishing_button.data("stats-url");
  var processing_animation_image = $("img", finishing_button);
  var finishing_icon = $("span", finishing_button);
  var day_completed_flag = $(".day-completed-flag");
  var day_completed_inner_flag = $("span.glyphicon", day_completed_flag);
  var point_list = $("#point-list");
  var UpdateDayCompletedFlag = function (data) {
    if (data.completed == "1") {
      day_completed_flag
        .attr("title", "Завершён")
        .removeClass("label-primary")
        .addClass("label-success");
      day_completed_inner_flag
        .removeClass("glyphicon-unchecked")
        .addClass("glyphicon-check");
    } else {
      day_completed_flag
        .attr("title", "Не завершён")
        .removeClass("label-success")
        .addClass("label-primary");
      day_completed_inner_flag
        .removeClass("glyphicon-check")
        .addClass("glyphicon-unchecked");
    }
  };
  var UpdateDaySatisfiedView = function (data) {
    var text = "&mdash;";
    if (data.satisfied != -1) {
      text = data.satisfied + "%";
    }

    $(".day-satisfied-view").html(text);
  };
  var UpdateDayStats = function () {
    $.get(
      stats_url,
      function (data) {
        UpdateDayCompletedFlag(data);
        UpdateDaySatisfiedView(data);
      },
      "json"
    ).fail(AjaxErrorDialog.handler);
  };

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
          UpdateDayStats();

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
