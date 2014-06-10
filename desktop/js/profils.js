
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
    $("#bt_saveProfils").on('click', function(event) {
        $.hideAlert();
        var profil = $('body').getValues('.userAttr');
        jeedom.user.saveProfils(profil[0], function() {
            $('#div_alert').showAlert({message: "{{Sauvegarde effectuée}}", level: 'success'});
            jeedom.user.get(function(data) {
                $('body').setValues(data, '.userAttr');
                modifyWithoutSave = false;
            });
        });
        return false;
    });

    jeedom.user.get(function(data) {
        $('body').setValues(data, '.userAttr');
        modifyWithoutSave = false;
    });

    $('body').delegate('.userAttr', 'change', function() {
        modifyWithoutSave = true;
    });
});


