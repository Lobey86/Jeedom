
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
    var scriptsCache = [];
    $.include = function(_path, _callback) {
        for (var i in _path) {
            if (jQuery.inArray(_path[i], scriptsCache) == -1) {
                var extension = _path[i].substr(_path[i].length - 3);
                if (extension == 'css') {
                    $('<link rel="stylesheet" href="' + _path[i] + '" type="text/css" />').appendTo('head');
                }
                if (extension == '.js') {
                    $('<script type="text/javascript" src="' + _path[i] + '"></script>').appendTo('head');
                }
                scriptsCache.push(_path[i]);
            }
        }
        _callback();
        return;
    };
})(jQuery);


