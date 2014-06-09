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

$(function() {
    if (view_id != '') {
        var html = jeedom.view.toHtml(view_id, 'dashboard');
        $('#div_displayView').empty().html(html.html);

        setTimeout(function() {
            positionEqLogic();
            $('.eqLogicZone').each(function() {
                $(this).masonry({columnWidth: 1});
            });
        }, 1);
    }
});