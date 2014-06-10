
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */


(function($) {
    function init(_value, _default) {
        if (!isset(_default)) {
            _default = '';
        }
        if (!isset(_value)) {
            return _default;
        }
        return _value;
    }

    function isset() {
        var a = arguments,
                l = a.length,
                i = 0,
                undef;

        if (l === 0) {
            throw new Error('Empty isset');
        }

        while (i !== l) {
            if (a[i] === undef || a[i] === null) {
                return false;
            }
            i++;
        }
        return true;
    }


    $.fn.showAlert = function(_options) {
        var options = init(_options, {});
        options.message = init(options.message, '');
        options.level = init(options.level, '');
        options.emptyBefore = init(options.emptyBefore, true);
        options.show = init(options.show, true);
        options.fix = init(options.fix, true);
        if ($.mobile) {
            $(this).empty();
            $(this).addClass('jqAlert');
            $(this).html('<a href="#" data-rel="back" data-role="button" data-theme="h" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>' + options.message);
            $(this).enhanceWithin().popup();
            $(this).popup('open');
        } else {
            if (options.emptyBefore == false) {
                var html = $(this).find('.displayError').html();
                if (isset(html)) {
                    options.message = html + '<br/>' + options.message;
                }
            }
            $(this).empty();
            $(this).html('<span href="#" class="btn_closeAlert pull-right cursor" style="position : relative; left : 30px;color : grey">×</span><span class="displayError">' + options.message + '</span>');
            $(this).removeClass('alert alert-warning alert-danger alert-info alert-success jqAlert');
            $(this).addClass('alert jqAlert');
            if (options.level != '') {
                $(this).addClass('alert-' + options.level);
            }
            if (options.show) {
                $(this).show();
                $(this).css('padding', '7px 35px 7px 15px');
                $(this).css('margin-bottom', '5px');
                $(this).css('overflow', 'auto');
                $(this).css('max-height', $(window).height() - 100 + 'px');
            }
            $(this).find('.btn_closeAlert').on('click', function() {
                $(this).closest('.jqAlert').hide();
            });
        }
        //Hide/show debug trace
        $(this).find('.bt_errorShowTrace').on('click', function() {
            var errorTrace = $(this).parent().find('.pre_errorTrace');
            if (errorTrace.is(':visible')) {
                errorTrace.hide();
                $(this).text('Show traces');
            } else {
                errorTrace.show();
                $(this).text('Hide traces');
            }
        });
        return this;
    };

    $.fn.hideAlert = function() {
        $('#jqAlertSpacer' + $(this).attr('id')).remove();
        $(this).text('').hide();
        return $(this);
    };

    $.hideAlert = function() {
        if ($.mobile) {
            $('.jqAlert').popup("close");
        } else {
            $('.jqAlert').text('');
            $('.jqAlertSpacer').remove();
            $('.jqAlert').hide();
        }
    };
})(jQuery);


