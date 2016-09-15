/**
 * Dreamstone Project base JS file
 */

var engine = function() {
  "use strict";
}

engine.prototype = {
  alert: function(message, callback) {
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
    modal.on('hidden.bs.modal', function() {
      if (callback) {
        callback();
      }
    });
  },

  confirm: function(message, callback) {
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
    modal.find('.confirm-yes').on('click', function() {
      if (callback) {
        callback();
      }
    });
  },

  enableStatusButtons: function() {
    var $self = this;
    $('.status-change-button').on('click', function(e) {
      e.preventDefault();
      var button = $(this),
          status = button.attr('data-status') == 1,
          enableMessage = button.attr('data-enable-message'),
          disableMessage = button.attr('data-disable-message'),
          id = button.attr('data-id'),
          url = button.attr('href'),
          message = (status ? disableMessage : enableMessage);

      $self.confirm(message, function() {
        $.ajax({
          url: url,
          type: 'POST',
          dataType: 'JSON',
          data: {
            id: id
          },
          success: function(data) {
            $self.alert(data.message);
            button.attr('data-status', data.status ? '1' : '0');
            if (data.status) {
              button.removeClass('btn-danger').addClass('btn-success');
              button.find('i').removeClass('fa-times-circle').addClass('fa-check-circle');
            } else {
              button.removeClass('btn-success').addClass('btn-danger');
              button.find('i').removeClass('fa-check-circle').addClass('fa-times-circle');
            }
          },
        });
      });
    });
  }
};
