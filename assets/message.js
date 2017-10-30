var $ = require("jquery");
var i18next = require('i18next');

module.exports = {
    showDialog: function(MessageService, userId, message, sentCallback, cancelCallback) {
        
        var self = this;
        
        var $modal = $(require('./message/modal.html'));
        
        var $form = $modal.find('form');
        
        var $btnSend = $form.find('.btn-primary').button();
        var $btnCancel = $form.find('.cancel').button();
        var $textarea = $form.find('textarea');
        
        $modal.find('.modal-title').text(i18next.t("personal-message-dialog/title"));
        $btnSend.attr('data-loading-text', i18next.t("personal-message-dialog/sending"));
        $btnSend.attr('data-complete-text', i18next.t("personal-message-dialog/sent"));
        $btnSend.attr('data-send-text', i18next.t("personal-message-dialog/send"));
        $btnSend.text(i18next.t("personal-message-dialog/send"));
        $btnCancel.text(i18next.t("personal-message-dialog/cancel"));
        $textarea.attr('placeholder', i18next.t("personal-message-dialog/placeholder"));
        
        if (message) {
            $textarea.val(message);
        }
        
        $modal.modal({
            show: true
        });

        $modal.on('hidden.bs.modal', function () {
            $modal.remove();
            if (cancelCallback) {
                cancelCallback();
            }
        });
        $modal.on('shown.bs.modal', function () {
            $textarea.focus();
        });
        
        
        $textarea.bind('change keyup click', function() {
            $textarea.parent().removeClass('error');
            $btnSend.text(i18next.t("personal-message-dialog/send"))
                .removeClass('btn-success')
                .prop('disabled', $(this).val().length <= 0);
        }).triggerHandler('change');
        
        $form.find('button.cancel, a.close').on('click', function(e) {
            e.preventDefault();
            $modal.modal('hide');
        });
        
        $form.submit(function(e) {
            e.preventDefault();
            
            var text = $textarea.val();
            
            if (text.length <= 0) {
                $textarea.parent().addClass('error');
            } else {
                $btnSend.button('loading');
                $btnCancel.prop("disabled", 1);
                $textarea.prop("disabled", 1);
                
                MessageService.send(userId, text).then(function() {
                    $textarea.val('');
                    
                    $btnSend.button('reset').button('complete').addClass('btn-success disabled').prop("disabled", 1);
                    
                    $textarea.prop("disabled", 0);
                    $btnCancel.prop("disabled", 0);
                    
                    if (sentCallback) {
                        sentCallback();
                    }
                });
            }
        });
    },
    sendMessage: function(userId, text, success) {
        $.post('/api/message', {user_id: userId, text: text}, function() {
            if (success) {
                success();
            }
        });
    }
};