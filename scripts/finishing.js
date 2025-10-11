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
    var is_completed = data.completed == "1";
    var is_skipped =
      is_completed && data.skipped == "1" && parseInt(data.daily, 10) > 0;

    if (is_skipped) {
      day_completed_flag
        .attr("title", "Пропущен")
        .removeClass("label-primary label-success")
        .addClass("label-default");
      day_completed_inner_flag
        .removeClass("glyphicon-unchecked glyphicon-check")
        .addClass("glyphicon-modal-window");
    } else if (is_completed) {
      day_completed_flag
        .attr("title", "Завершён")
        .removeClass("label-primary label-default")
        .addClass("label-success");
      day_completed_inner_flag
        .removeClass("glyphicon-unchecked glyphicon-modal-window")
        .addClass("glyphicon-check");
    } else {
      day_completed_flag
        .attr("title", "Не завершён")
        .removeClass("label-success label-default")
        .addClass("label-primary");
      day_completed_inner_flag
        .removeClass("glyphicon-check glyphicon-modal-window")
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

  finishing_button.click(function (event) {
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

    event.preventDefault();
    event.stopPropagation();
    return false;
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
