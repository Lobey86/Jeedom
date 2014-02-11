<?php

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

require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}

if (init('path') == '') {
    throw new Exception('Aucun widget fourni');
}
$widget = widget::byPath(init('path'));
if (!is_object($widget)) {
    throw new Exception('Widget non trouvé');
}


include_file('3rdparty', 'font-awesome/css/font-awesome.min', 'css');
include_file('3rdparty', 'highstock/highstock', 'js');
include_file('3rdparty', 'highstock/highcharts-more', 'js');
include_file('3rdparty', 'php.js/php.min', 'js');
include_file('3rdparty', 'jquery/jquery.min', 'js');
include_file('3rdparty', 'jquery.include/jquery.include', 'js');

if ($widget->getVersion() == 'mobile') {
    include_file('3rdparty', 'jquery.mobile/jquery.mobile', 'css');
    include_file('mobile', 'commun', 'css');
    include_file('3rdparty', 'jquery.mobile/jquery.mobile.min', 'js');
} else {
    include_file('desktop', 'commun', 'css');
    include_file('3rdparty', 'bootstrap/bootstrap.min', 'css');
    include_file('3rdparty', 'jquery.gritter/jquery.gritter', 'css');
    include_file('3rdparty', 'jquery.ui/jquery-ui-bootstrap/jquery-ui', 'css');
    include_file('3rdparty', 'jquery.loading/jquery.loading', 'css');
    include_file('3rdparty', 'bootstrap/bootstrap.min', 'js');
    include_file('3rdparty', 'jquery.ui/jquery-ui.min', 'js');
    include_file('3rdparty', 'jquery.value/jquery.value', 'js');
    include_file('3rdparty', 'jquery.alert/jquery.alert', 'js');
    include_file('3rdparty', 'jquery.loading/jquery.loading', 'js');
    include_file('core', 'js.inc', 'php');
}
echo '<center style="margin-top : 10px;">';
echo $widget->displayExemple();
echo '</center>';
?>
