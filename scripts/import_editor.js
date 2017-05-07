$(document).ready(function() {
  var import_editor = ace.edit('import-editor')
  import_editor.$blockScrolling = Infinity
  import_editor.setTheme('ace/theme/pastel_on_dark')
  import_editor.getSession().setMode('ace/mode/markdown')
  import_editor.setShowInvisibles(true)
  import_editor.setShowPrintMargin(false)
  import_editor.focus()

  var import_mobile_editor = $('#import-mobile-editor')
  $('a[data-toggle="tab"]').on('show.bs.tab', function(event) {
    switch ($(event.target).attr('href')) {
      case '#default':
        import_editor.setValue(import_mobile_editor.val(), -1)
        break
      case '#mobile':
        import_mobile_editor.val(import_editor.getValue())
        break
    }
  })
  if (new MobileDetect(navigator.userAgent).mobile()) {
    $('a[href="#mobile"]').tab('show')
  }

  $('.import-button').click(function() {
    var points_description = $('#points-description')
    switch ($('.tab-pane.active').attr('id')) {
      case 'default':
        points_description.val(import_editor.getValue())
        break
      case 'mobile':
        points_description.val(import_mobile_editor.val())
        break
    }

    $('#import-form').submit()
  })
})
