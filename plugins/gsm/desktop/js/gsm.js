
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
    $('#bt_addPhoneNumber').on('click', function() {
        addAllowPhoneNumber();
    });

    $('#div_listPhoneNumber').delegate('.bt_removeAllowPhone', 'click', function() {
        $(this).closest('div').remove();
    });
});

function addEqLogic(_eqLogic) {
    if (!is_array(_eqLogic.configuration.allowPhoneNumber)) {
        addAllowPhoneNumber(_eqLogic.configuration.allowPhoneNumber)
    } else {
        for (var i in _eqLogic.configuration.allowPhoneNumber) {
            addAllowPhoneNumber(_eqLogic.configuration.allowPhoneNumber[i])
        }
    }
}

function addAllowPhoneNumber(_phoneNumber) {
    var input = '<div style="margin-top : 5px;">';
    input += '<input type="text" class="eqLogicAttr form-inline" data-l1key="configuration" data-l2key="allowPhoneNumber" value="' + init(_phoneNumber) + '" />';
    input += '<a class="bt_remove"></a>';
    input += ' <i class="fa fa-minus-circle bt_removeAllowPhone cursor"></i></td>';
    input += '<div>';
    $('#div_listPhoneNumber').append(input);
}