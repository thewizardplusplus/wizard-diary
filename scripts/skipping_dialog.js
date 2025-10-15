var SkippingDialog = {};

$(document).ready(function () {
  var skipping_dialog = $(".skipping-dialog");
  var ok_button = $(".ok-button", skipping_dialog);

  SkippingDialog = {
    show: function (ok_button_handler) {
      ok_button.off("click");
      ok_button.click(ok_button_handler);

      skipping_dialog.modal("show");
    },
    hide: function () {
      skipping_dialog.modal("hide");
    },
  };
});
