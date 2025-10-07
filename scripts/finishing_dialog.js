var FinishingDialog = {};

$(document).ready(function () {
  var finishing_dialog = $(".finishing-dialog");
  var ok_button = $(".ok-button", finishing_dialog);

  FinishingDialog = {
    show: function (ok_button_handler) {
      ok_button.off("click");
      ok_button.click(ok_button_handler);

      finishing_dialog.modal("show");
    },
    hide: function () {
      finishing_dialog.modal("hide");
    },
  };
});
