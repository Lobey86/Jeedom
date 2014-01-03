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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');
    if (!isConnect()) {
        throw new Exception('401 Unauthorized');
    }

    if (init('action') == 'get') {
        $widget = widget::byPath(init('path'));
        if (!is_object($widget)) {
            throw new Exception('Widget non trouvé');
        }
        ajax::success(utils::o2a($widget));
    }

    if (init('action') == 'save') {
        $widget_ajax = json_decode(init('widget'), true);
        if (file_exists($widget['path'])) {
            $widget_db = widget::byPath($widget['path']);
            if (!is_object($widget_db)) {
                $widget_db = new widget();
            }
        } else {
            $widget_db = new widget();
        }
        utils::a2o($widget_db, $widget_ajax);
        $widget_db->save();
        ajax::success(utils::o2a($widget_db));
    }

    if (init('action') == 'add') {
        $widget = new widget();
        $widget->setName(init('name'));
        $widget->save();
        ajax::success(utils::o2a($widget));
    }

    if (init('action') == 'remove') {
        if (file_exists(init('path'))) {
            $widget = widget::byPath(init('path'));
            if (is_object($widget)) {
                $widget->remove();
                ajax::success();
            }
        }
        throw new Exception('Widget non trouvé : ' . init('path'));
    }

    throw new Exception('Aucune methode correspondante à : ' . init('action'));
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
