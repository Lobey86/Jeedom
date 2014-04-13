<?php
if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
$view = null;
if (init('id') == '') {
    if ($_SESSION['user']->getOptions('defaultMobileView') != '') {
        $view = view::byId($_SESSION['user']->getOptions('defaultMobileView'));
    }
    if (!is_object($view)) {
        $list_view = view::all();
        $view = $list_view[0];
    }
} else {
    $view = view::byId(init('id'));
}
if (!is_object($view)) {
    throw new Exception('{{Vue inconnue. Verifier l\'id}}');
}

global $rightPanel;
$rightPanel = '<ul data-role="listview" data-theme="a" data-dividertheme="a" class="ul_object ui-icon-alt">';
foreach (view::all() as $view_info) {
    $rightPanel .= '<li class="li_view"><a href="index.php?v=m&p=view&id=' . $view_info->getId() . '">' . $view_info->getName() . '</a></li>';
}
$rightPanel .= '</ul>';
?>
<div>
    <legend style="color : #60a725;">{{Vue}} <?php echo $view->getName() ?></legend>
    <?php
    foreach ($view->getviewZone() as $viewZone) {
        echo '<div>';
        echo '<legend style="color : #716b7a">' . $viewZone->getName() . '</legend>';
        $div_id = 'div_viewZone' . $viewZone->getId();
        /*         * *****************viewZone widget***************** */
        if ($viewZone->getType() == 'widget') {
            echo '<div id="' . $div_id . '">';
            foreach ($viewZone->getViewData() as $viewData) {
                echo $viewData->getLinkObject()->toHtml('mobile');
            }
            echo '</div>';
        }

        /*         * *****************viewZone graph***************** */
        if ($viewZone->getType() == 'graph') {
            echo '<div id="' . $div_id . '">';
            echo '<script>';
            echo '$(document).on("pagecontainershow", function() {';
            foreach ($viewZone->getViewData() as $viewData) {
                echo 'drawChart(' . $viewData->getLink_id() . ',"' . $div_id . '","' . $viewZone->getConfiguration('dateRange') . '",jQuery.parseJSON("' . addslashes(json_encode($viewData->getConfiguration())) . '"));';
            }
            echo '});';
            echo '</script>';
            echo '</div>';
        }
        echo '</div>';
    }
    ?>
</div>
<?php include_file('mobile', 'view', 'js'); ?>
