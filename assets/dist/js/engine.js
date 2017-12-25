/**
 * Dreamstone Project base JS file
 */

window.engine = function () {
    "use strict";
};

engine.prototype = {
    alert: function (message, callback) {
        var alertTemplate = '\
      <div class="modal" tabindex="-1" role="dialog">\
      <div class="modal-dialog" role="document">\
        <div class="modal-content">\
          <div class="modal-header">\
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
            <h4 class="modal-title">Alert</h4>\
          </div>\
          <div class="modal-body">\
            <p>' + message + '</p>\
          </div>\
          <div class="modal-footer">\
            <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="OK">OK</button>\
          </div>\
        </div>\
      </div>\
    </div>\
    ';

        var modal = $(alertTemplate).prependTo($('body'));
        modal.modal('show');
        modal.on('hidden.bs.modal', function () {
            if (callback) {
                callback();
            }
        });
    },

    confirm: function (message, callback) {
        var confirmTemplate = '\
      <div class="modal" tabindex="-1" role="dialog">\
      <div class="modal-dialog" role="document">\
        <div class="modal-content">\
          <div class="modal-header">\
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
            <h4 class="modal-title">Alert</h4>\
          </div>\
          <div class="modal-body">\
            <p>' + message + '</p>\
          </div>\
          <div class="modal-footer">\
            <button type="button" class="btn btn-primary confirm-yes" data-dismiss="modal" aria-label="Yes">Yes</button>\
            <button type="button" class="btn btn-danger confirm-no" data-dismiss="modal" aria-label="No">No</button>\
          </div>\
        </div>\
      </div>\
    </div>\
    ';

        var modal = $(confirmTemplate).prependTo($('body'));
        modal.modal('show');
        modal.find('.confirm-yes').on('click', function () {
            if (callback) {
                callback();
            }
        });
    },

    enableStatusButtons: function () {
        var $self = this;
        $('.status-change-button').on('click', function (e) {
            e.preventDefault();
            var button = $(this),
                status = button.attr('data-status') == 1,
                enableMessage = button.attr('data-enable-message'),
                disableMessage = button.attr('data-disable-message'),
                id = button.attr('data-id'),
                url = button.attr('href'),
                message = (status ? disableMessage : enableMessage);

            $self.confirm(message, function () {
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        id: id
                    },
                    success: function (data) {
                        $self.alert(data.message);
                        button.attr('data-status', data.status ? '1' : '0');
                        if (data.status) {
                            button.removeClass('btn-danger').addClass('btn-success');
                            button.find('i').removeClass('fa-times-circle').addClass('fa-check-circle');
                        } else {
                            button.removeClass('btn-success').addClass('btn-danger');
                            button.find('i').removeClass('fa-check-circle').addClass('fa-times-circle');
                        }
                    }
                });
            });
        });
    },

    enableDateTimePicker: function() {
        $('.datetimepicker').datetimepicker({
            locale: 'pt-br',
            format: 'YYYY-MM-DD HH:mm'
        });

        $('.datepicker').datetimepicker({
            locale: 'pt-br',
            format: 'DD/MM/YYYY'
        });
    },

    enableTinyMCE: function() {
        tinymce.init({
            selector: '.tinymce-basic',
            menubar: false,
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save();
                });
            },
            external_filemanager_path: "/assets/plugins/tinymce/plugins/filemanager/",
            filemanager_title: "Responsive Filemanager",
            external_plugins: {"filemanager": "/assets/plugins/tinymce/plugins/filemanager/plugin.min.js"},
        });

        tinymce.init({
            selector: '.tinymce',
            menubar: false,
            plugins: [
                "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
                "table contextmenu directionality emoticons paste textcolor responsivefilemanager code"
            ],
            toolbar1: "undo redo | bold italic underline fontselect fontsizeselect | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
            toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code ",
            image_advtab: true,
            content_css: '/assets/dist/css/default.css,/assets/dist/css/fonts.css',
            external_filemanager_path: "/assets/plugins/tinymce/plugins/filemanager/",
            filemanager_title: "Responsive Filemanager",
            external_plugins: {"filemanager": "/assets/plugins/tinymce/plugins/filemanager/plugin.min.js"},
            relative_urls: false,
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save();
                });
            }
        });
    },

    enableTagsInput: function() {
        $('.tagsinput').tagsinput();
    },

    enableCharts: function() {
        var ctx = $('#report');

        if (ctx.length) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [window.reportDates],
                    datasets: [{
                        label: 'Reads',
                        data: [window.reportReads],
                        backgroundColor: 'rgba(145,220,0,0.3)',
                        borderColor: 'rgb(145,220,0)'
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                userCallback: function (label) {
                                    if (Math.floor(label) === label) {
                                        return label;
                                    }
                                }
                            }
                        }]
                    }
                }
            });
        }
    }
};
