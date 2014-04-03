<?php
if (!isConnect()) {
    throw new Exception('401 - Unauthorized access to page');
}

if (init('id') == '') {
    if ($_SESSION['user']->getOptions('defaultDesktopView') != '') {
        $view = view::byId($_SESSION['user']->getOptions('defaultDesktopView'));
        if (is_object($view)) {
            redirect('index.php?v=d&p=view&id=' . $view->getId());
        }
    }
    $list_view = view::all();
    if (is_object($list_view[0])) {
        redirect('index.php?v=d&p=view&id=' . $list_view[0]->getId());
    }
}
if (init('id') != '') {
    $view = view::byId(init('id'));
    if (!is_object($view)) {
        throw new Exception('Vue inconnue. Verifier l\'id');
    }
} else {
    redirect('index.php?v=d&p=view_edit');
}
?>

<div class="row">
    <div class="col-lg-2">
        <div class="bs-sidebar affix">
            <ul id="ul_view" class="nav nav-list bs-sidenav">
                <li class="nav-header">Liste des vues</li>
                <li class="filter"><input class="filter form-control input-sm" placeholder="Rechercher" style="width: 100%"/></li>
                <?php
                foreach (view::all() as $view_info) {
                    if ($view->getId() == $view_info->getId()) {
                        echo '<li class="cursor li_view active"><a href="index.php?v=d&p=view&id=' . $view_info->getId() . '">' . $view_info->getName() . '</a></li>';
                    } else {
                        echo '<li class="cursor li_view"><a href="index.php?v=d&p=view&id=' . $view_info->getId() . '">' . $view_info->getName() . '</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>

    <div class="col-lg-10" role="main">
        <legend style="height: 35px;color : #563d7c;">Vue <?php echo $view->getName() ?> <a href="index.php?v=d&p=view_edit&id=<?php echo $view->getId(); ?>" class="btn btn-warning btn-xs pull-right" id="bt_addviewZone"><i class="fa fa-pencil"></i> Editer</a></legend>
        <?php
        foreach ($view->getviewZone() as $viewZone) {
            echo '<div>';
            echo '<legend style="color : #716b7a">' . $viewZone->getName() . '</legend>';
            $div_id = 'div_viewZone' . $viewZone->getId();
            /*             * *****************viewZone widget***************** */
            if ($viewZone->getType() == 'widget') {
                echo '<div id="' . $div_id . '">';
                foreach ($viewZone->getViewData() as $viewData) {
                    echo $viewData->getLinkObject()->toHtml('dashboard');
                }
                echo '</div>';
            }

            /*             * *****************viewZone graph***************** */
            if ($viewZone->getType() == 'graph') {
                echo '<div id="' . $div_id . '">';
                echo '<script>';
                echo '$(function() {';
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

</div>
