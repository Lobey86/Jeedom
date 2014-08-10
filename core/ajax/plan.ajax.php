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
    require_once(dirname(__FILE__) . '/../../core/php/core.inc.php');
    include_file('core', 'authentification', 'php');

    if (!isConnect()) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

    if (init('action') == 'save') {
        $plans = json_decode(init('plans'), true);
        foreach ($plans as $plan_ajax) {
            $plan = plan::byId($plan_ajax['id']);
            if (!is_object($plan)) {
                $plan = plan::byLinkTypeLinkIdObjectId($plan_ajax['link_type'], $plan_ajax['link_id'], $plan_ajax['object_id']);
                if (!is_object($plan)) {
                    $plan = new plan();
                }
            }
            utils::a2o($plan, $plan_ajax);
            $plan->save();
        }
        ajax::success();
    }

    if (init('action') == 'byObject') {
        ajax::success(utils::o2a(plan::byObjectId(init('object_id'))));
    }

    throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
