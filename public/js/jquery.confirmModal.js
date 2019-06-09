/*!
 * jQuery.confirmModal v1.0
 * Copyright (c) 2018 Trim C.
 * Released under the MIT license
 * Description : Easy and simple to use plugin replacing the browser's default confirm box with bootstrap 4 modal
 */
(function ($) {
    $currentModalTarget = {};
    $defaultsConfirmModal = {};
    $optionsConfirmModal = {};
    jQuery.confirmModal = function(message, callback) {
        var targetElement = document.activeElement;

        if (!$isMobile) {
            $(document).on('shown.bs.modal', '.modalConfirm', function () { $('.confirmButton').focus(); });
        }

        var settings = $.extend({}, $defaultsConfirmModal, $optionsConfirmModal);

        if (settings === undefined) {
            var modalBoxWidth = 'auto';
            var modalVerticalCenter = '';
            var fadeAnimation = '';
        } else {
            if (settings.modalBoxWidth === undefined) {
                var modalBoxWidth = 'auto';
            } else {
                var modalBoxWidth = settings.modalBoxWidth;
            }
            if (settings.modalVerticalCenter === undefined || settings.modalVerticalCenter === false) {
                var modalVerticalCenter = '';
            } else {
                var modalVerticalCenter = 'modal-dialog-centered';
            }
            if (settings.messageHeader === undefined) {
                var messageHeader = '';
            } else {
                var messageHeader = settings.messageHeader;
            }
            if (settings.fadeAnimation === undefined || settings.fadeAnimation === false) {
                var fadeAnimation = '';
            } else {
                var fadeAnimation = 'fade';
            }
            if (settings.blur !== undefined || settings.blur === true) {
                $('.edit_image_mobile').addClass('blur');
            }
        }

        if ($locale === 'en') {
            var confirmButton = 'OK';
            var cancelButton = 'Cancel';
        } else {
            var confirmButton = 'OK';
            var cancelButton = 'Annuler';
        }

        var html = `
            <div style="z-index: 5000;" class="modal ` + fadeAnimation + ` modalConfirm" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
                <div style="max-width: ` + modalBoxWidth + `;" class="modal-dialog ` + modalVerticalCenter + `" role="document">
                    <div class="modal-content">
                        <div style="padding: 0.6rem" class="modal-header">
                            <h6 class="modal-title">` + messageHeader + `</h6>
                            <button type="button" style="padding: 0.45rem 0.8rem;" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div style="padding: 0.8rem; font-size: 0.89rem;" class="modal-body">` + message + `</div>
                        <div style="padding: 0.55rem;" class="noselect modal-footer">
                            <button type="button" style="width: 66px; padding: .18rem .5rem;" class="confirmButton btn btn-primary btn-sm">` + confirmButton + `</button>
                            <button type="button" style="padding: .18rem .5rem;" class="btn btn-secondary btn-sm" data-dismiss="modal">` + cancelButton + `</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        if ($.isEmptyObject($currentModalTarget)) {
            $currentModalTarget = targetElement;
            $('body').prepend(html);
            $('.modalConfirm').modal('show');
            if (settings.blur === true) {
                $('.modalConfirm').on('hide.bs.modal', function () { $('.edit_image_mobile').removeClass('blur'); });
            }
            $('.confirmButton').on('click', function(e) {
                e.preventDefault();
                $('.modalConfirm').modal('hide');
                callback(targetElement);
            });
        } else if ($currentModalTarget.className != targetElement.className) {
            $('body > div.modalConfirm').remove();
            $currentModalTarget = targetElement;
            $('body').prepend(html);
            $('.modalConfirm').modal('show');
            if (settings.blur === true) {
                $('.modalConfirm').on('hide.bs.modal', function () { $('.edit_image_mobile').removeClass('blur'); });
            }
            $('.confirmButton').off('click');
            $('.confirmButton').on('click', function(e) {
                e.preventDefault();
                $('.modalConfirm').modal('hide');
                callback(targetElement);
            });
        } else if (targetElement != $currentModalTarget) {
            $currentModalTarget = targetElement;
            $('.modalConfirm').modal('show');
            if (settings.blur === true) {
                $('.modalConfirm').on('hide.bs.modal', function () { $('.edit_image_mobile').removeClass('blur'); });
            }
            $('.confirmButton').off('click');
            $('.confirmButton').on('click', function(e) {
                e.preventDefault();
                $('.modalConfirm').modal('hide');
                callback(targetElement);
            });
        } else {
            $('.modalConfirm').modal('show');
        }
    };
}(jQuery));
