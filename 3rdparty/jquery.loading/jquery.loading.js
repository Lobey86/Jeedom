
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
    $.showLoading = function() {
        if ($.mobile) {
            $.mobile.loading('show', {
                text: 'Chargement...',
                textVisible: true,
            });
        } else {
            if ($('#jqueryLoadingDiv').length == 0) {
                $('body').append('<div id="jqueryLoadingDiv"><div class="overlay"></div><img class="loadingImg" /></div>');
            }
            $('#jqueryLoadingDiv').show();
        }
    };
    $.hideLoading = function() {
        if ($.mobile) {
            $.mobile.loading('hide');
        } else {
            $('#jqueryLoadingDiv').hide();
        }
    };
})(jQuery);


