(function (window, document, $, undefined) {
    "use strict";

    var modalTemplate =
        '<div class="modal fade" role="dialog" id="delete-language-confirm-dialog">' +
            '<div class="modal-dialog">' +
                '<div class="modal-content">' +
                    '<div class="modal-header">' +
                        '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
                        '<h4 class="modal-title">Delete Language</h4>' +
                    '</div>' +
                    '<div class="modal-body">' +
                        '<p>Are you sur you want to delete this language ?</p>' +
                    '</div>' +
                    '<div class="modal-footer">' +
                        '<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>' +
                        '<a href="#" class="btn btn-danger btn-modal btn-confirm-action" id="delete-language-confirm-action">Delete</a>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';

    function sendDeleteRequest(input) {
        var form = $(document.createElement('form'));
        $(form).attr('action', input.href);
        $(form).attr('method', 'POST');

        var inputMethod =$('<input>').attr('name', '_method').attr('type', 'hidden').attr('value', 'DELETE');
        var inputToken =$('<input>').attr('name', '_token').attr('type', 'hidden').attr('value', input.token);

        $(form).append(inputMethod);
        $(form).append(inputToken);

        form.appendTo(document.body);
        $(form).submit();
    }

    $(document).ready(function () {
        var buttons, modalBox, confirmButton, data;

        buttons = $('.btn-delete-confirm');
        $('body').append($.parseHTML(modalTemplate));
        modalBox = $('#delete-language-confirm-dialog');
        confirmButton = $('#delete-language-confirm-action');

        data = {};

        /**
         * Delete Buttons Click event
         */
        buttons.on('click', function (event) {
            event.preventDefault();

            var elm = $(this);
            data.token = elm.data('token');
            data.href =elm.attr('href');
            modalBox.modal();
        });

        /**
         * Confirm Button Delete Click event
         */
        confirmButton.on('click', function () {
            sendDeleteRequest(data);
        });
    });


}(this, this.document, jQuery));
