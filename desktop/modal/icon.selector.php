<?php
if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
?>

<style>
    .iconSel{
        float: left;
        width: 7%;
        float: left;
        padding: 10px;
        line-height: 1.4;
        text-align: center;
        border: 1px solid #fff;
        box-sizing: border-box;
        display: list-item;
        list-style: none;
        font-size: 1.5em;
        cursor: pointer;
    }

    .iconSelected{
        background-color: #563d7c;
        color: white;
    }
</style>
<?php
foreach (ls('core/css/icon', '*') as $dir) {
    if (is_dir('core/css/icon/' . $dir) && file_exists('core/css/icon/' . $dir . '/style.css')) {
        $css = file_get_contents('core/css/icon/' . $dir . '/style.css');
        $research = strtolower(str_replace('/', '', $dir));
        preg_match_all("/\." . $research . "-(.*?):/", $css, $matches, PREG_SET_ORDER);
        $height = (ceil(count($matches) / 14) * 40) + 80;
        echo '<div style="height : ' . $height . 'px;"><legend>{{' . str_replace('/', '', $dir) . '}}</legend>';

        foreach ($matches as $match) {
            if (isset($match[0])) {
                $icon = str_replace(array(':', '.'), '', $match[0]);
                echo '<li class="iconSel"><i class="icon ' . $icon . '"></i></li>';
            }
        }
        echo "</div><br/>";
    }
}
?>
<div style="height: 650px;">
    <legend>{{Générale}}</legend>
    <li class="iconSel"><i class="fa fa-glass"></i></li>
    <li class="iconSel"><i class="fa fa-music"></i></li>
    <li class="iconSel"><i class="fa fa-search"></i></li>
    <li class="iconSel"><i class="fa fa-envelope-o"></i></li>
    <li class="iconSel"><i class="fa fa-heart"></i></li>
    <li class="iconSel"><i class="fa fa-star"></i></li>
    <li class="iconSel"><i class="fa fa-star-o"></i></li>
    <li class="iconSel"><i class="fa fa-user"></i></li>
    <li class="iconSel"><i class="fa fa-film"></i></li>
    <li class="iconSel"><i class="fa fa-th-large"></i></li>
    <li class="iconSel"><i class="fa fa-check"></i></li>
    <li class="iconSel"><i class="fa fa-times"></i></li>
    <li class="iconSel"><i class="fa fa-power-off"></i></li>
    <li class="iconSel"><i class="fa fa-signal"></i></li>
    <li class="iconSel"><i class="fa fa-cog"></i></li>
    <li class="iconSel"><i class="fa fa-trash-o"></i></li>
    <li class="iconSel"><i class="fa fa-home"></i></li>
    <li class="iconSel"><i class="fa fa-file-o"></i></li>
    <li class="iconSel"><i class="fa fa-clock-o"></i></li>
    <li class="iconSel"><i class="fa fa-road"></i></li>
    <li class="iconSel"><i class="fa fa-download"></i></li>
    <li class="iconSel"><i class="fa fa-inbox"></i></li>
    <li class="iconSel"><i class="fa fa-play-circle-o"></i></li>
    <li class="iconSel"><i class="fa fa-refresh"></i></li>
    <li class="iconSel"><i class="fa fa-list-alt"></i></li>
    <li class="iconSel"><i class="fa fa-lock"></i></li>
    <li class="iconSel"><i class="fa fa-flag"></i></li>
    <li class="iconSel"><i class="fa fa-headphones"></i></li>
    <li class="iconSel"><i class="fa fa-volume-down"></i></li>
    <li class="iconSel"><i class="fa fa-qrcode"></i></li>
    <li class="iconSel"><i class="fa fa-barcode"></i></li>
    <li class="iconSel"><i class="fa fa-tag"></i></li>
    <li class="iconSel"><i class="fa fa-book"></i></li>
    <li class="iconSel"><i class="fa fa-print"></i></li>
    <li class="iconSel"><i class="fa fa-camera"></i></li>
    <li class="iconSel"><i class="fa fa-video-camera"></i></li>
    <li class="iconSel"><i class="fa fa-picture-o"></i></li>
    <li class="iconSel"><i class="fa fa-pencil"></i></li>
    <li class="iconSel"><i class="fa fa-map-marker"></i></li>
    <li class="iconSel"><i class="fa fa-tint"></i></li>
    <li class="iconSel"><i class="fa fa-pencil-square-o"></i></li>
    <li class="iconSel"><i class="fa fa-check-square-o"></i></li>
    <li class="iconSel"><i class="fa fa-arrows"></i></li>
    <li class="iconSel"><i class="fa fa-step-backward"></i></li>
    <li class="iconSel"><i class="fa fa-fast-backward"></i></li>
    <li class="iconSel"><i class="fa fa-backward"></i></li>
    <li class="iconSel"><i class="fa fa-play"></i></li>
    <li class="iconSel"><i class="fa fa-pause"></i></li>
    <li class="iconSel"><i class="fa fa-stop"></i></li>
    <li class="iconSel"><i class="fa fa-forward"></i></li>
    <li class="iconSel"><i class="fa fa-fast-forward"></i></li>
    <li class="iconSel"><i class="fa fa-step-forward"></i></li>
    <li class="iconSel"><i class="fa fa-eject"></i></li>
    <li class="iconSel"><i class="fa fa-chevron-left"></i></li>
    <li class="iconSel"><i class="fa fa-chevron-right"></i></li>
    <li class="iconSel"><i class="fa fa-plus-circle"></i></li>
    <li class="iconSel"><i class="fa fa-minus-circle"></i></li>
    <li class="iconSel"><i class="fa fa-times-circle"></i></li>
    <li class="iconSel"><i class="fa fa-check-circle"></i></li>
    <li class="iconSel"><i class="fa fa-question-circle"></i></li>
    <li class="iconSel"><i class="fa fa-info-circle"></i></li>
    <li class="iconSel"><i class="fa fa-crosshairs"></i></li>
    <li class="iconSel"><i class="fa fa-times-circle-o"></i></li>
    <li class="iconSel"><i class="fa fa-check-circle-o"></i></li>
    <li class="iconSel"><i class="fa fa-ban"></i></li>
    <li class="iconSel"><i class="fa fa-arrow-left"></i></li>
    <li class="iconSel"><i class="fa fa-arrow-right"></i></li>
    <li class="iconSel"><i class="fa fa-arrow-up"></i></li>
    <li class="iconSel"><i class="fa fa-arrow-down"></i></li>
    <li class="iconSel"><i class="fa fa-plus"></i></li>
    <li class="iconSel"><i class="fa fa-minus"></i></li>
    <li class="iconSel"><i class="fa fa-asterisk"></i></li>
    <li class="iconSel"><i class="fa fa-exclamation-circle"></i></li>
    <li class="iconSel"><i class="fa fa-gift"></i></li>
    <li class="iconSel"><i class="fa fa-leaf"></i></li>
    <li class="iconSel"><i class="fa fa-fire"></i></li>
    <li class="iconSel"><i class="fa fa-eye"></i></li>
    <li class="iconSel"><i class="fa fa-eye-slash"></i></li>
    <li class="iconSel"><i class="fa fa-exclamation-triangle"></i></li>
    <li class="iconSel"><i class="fa fa-plane"></i></li>
    <li class="iconSel"><i class="fa fa-calendar"></i></li>
    <li class="iconSel"><i class="fa fa-random"></i></li>
    <li class="iconSel"><i class="fa fa-comment"></i></li>
    <li class="iconSel"><i class="fa fa-magnet"></i></li>
    <li class="iconSel"><i class="fa fa-chevron-up"></i></li>
    <li class="iconSel"><i class="fa fa-chevron-down"></i></li>
    <li class="iconSel"><i class="fa fa-shopping-cart"></i></li>
    <li class="iconSel"><i class="fa fa-folder"></i></li>
    <li class="iconSel"><i class="fa fa-folder-open"></i></li>
    <li class="iconSel"><i class="fa fa-bar-chart-o"></i></li>
    <li class="iconSel"><i class="fa fa-key"></i></li>
    <li class="iconSel"><i class="fa fa-heart-o"></i></li>
    <li class="iconSel"><i class="fa fa-sign-out"></i></li>
    <li class="iconSel"><i class="fa fa-thumb-tack"></i></li>
    <li class="iconSel"><i class="fa fa-sign-in"></i></li>
    <li class="iconSel"><i class="fa fa-phone"></i></li>
    <li class="iconSel"><i class="fa fa-unlock"></i></li>
    <li class="iconSel"><i class="fa fa-credit-card"></i></li>
    <li class="iconSel"><i class="fa fa-rss"></i></li>
    <li class="iconSel"><i class="fa fa-hdd-o"></i></li>
    <li class="iconSel"><i class="fa fa-bullhorn"></i></li>
    <li class="iconSel"><i class="fa fa-bell"></i></li>
    <li class="iconSel"><i class="fa fa-globe"></i></li>
    <li class="iconSel"><i class="fa fa-wrench"></i></li>
    <li class="iconSel"><i class="fa fa-filter"></i></li>
    <li class="iconSel"><i class="fa fa-briefcase"></i></li>
    <li class="iconSel"><i class="fa fa-cloud"></i></li>
    <li class="iconSel"><i class="fa fa-flask"></i></li>
    <li class="iconSel"><i class="fa fa-scissors"></i></li>
    <li class="iconSel"><i class="fa fa-paperclip"></i></li>
    <li class="iconSel"><i class="fa fa-floppy-o"></i></li>
    <li class="iconSel"><i class="fa fa-table"></i></li>
    <li class="iconSel"><i class="fa fa-magic"></i></li>
    <li class="iconSel"><i class="fa fa-truck"></i></li>
    <li class="iconSel"><i class="fa fa-money"></i></li>
    <li class="iconSel"><i class="fa fa-columns"></i></li>
    <li class="iconSel"><i class="fa fa-envelope"></i></li>
    <li class="iconSel"><i class="fa fa-gavel"></i></li>
    <li class="iconSel"><i class="fa fa-tachometer"></i></li>
    <li class="iconSel"><i class="fa fa-bolt"></i></li>
    <li class="iconSel"><i class="fa fa-sitemap"></i></li>
    <li class="iconSel"><i class="fa fa-umbrella"></i></li>
    <li class="iconSel"><i class="fa fa-lightbulb-o"></i></li>
    <li class="iconSel"><i class="fa fa-stethoscope"></i></li>
    <li class="iconSel"><i class="fa fa-suitcase"></i></li>
    <li class="iconSel"><i class="fa fa-coffee"></i></li>
    <li class="iconSel"><i class="fa fa-cutlery"></i></li>
    <li class="iconSel"><i class="fa fa-building-o"></i></li>
    <li class="iconSel"><i class="fa fa-medkit"></i></li>
    <li class="iconSel"><i class="fa fa-beer"></i></li>
    <li class="iconSel"><i class="fa fa-h-square"></i></li>
    <li class="iconSel"><i class="fa fa-desktop"></i></li>
    <li class="iconSel"><i class="fa fa-laptop"></i></li>
    <li class="iconSel"><i class="fa fa-tablet"></i></li>
    <li class="iconSel"><i class="fa fa-mobile"></i></li>
    <li class="iconSel"><i class="fa fa-circle-o"></i></li>
    <li class="iconSel"><i class="fa fa-spinner"></i></li>
    <li class="iconSel"><i class="fa fa-circle"></i></li>
    <li class="iconSel"><i class="fa fa-smile-o"></i></li>
    <li class="iconSel"><i class="fa fa-frown-o"></i></li>
    <li class="iconSel"><i class="fa fa-meh-o"></i></li>
    <li class="iconSel"><i class="fa fa-gamepad"></i></li>
    <li class="iconSel"><i class="fa fa-keyboard-o"></i></li>
    <li class="iconSel"><i class="fa fa-terminal"></i></li>
    <li class="iconSel"><i class="fa fa-location-arrow"></i></li>
    <li class="iconSel"><i class="fa fa-microphone"></i></li>
    <li class="iconSel"><i class="fa fa-microphone-slash"></i></li>
    <li class="iconSel"><i class="fa fa-shield"></i></li>
    <li class="iconSel"><i class="fa fa-calendar-o"></i></li>
    <li class="iconSel"><i class="fa fa-fire-extinguisher"></i></li>
    <li class="iconSel"><i class="fa fa-rocket"></i></li>
    <li class="iconSel"><i class="fa fa-anchor"></i></li>
    <li class="iconSel"><i class="fa fa-unlock-alt"></i></li>
    <li class="iconSel"><i class="fa fa-compass"></i></li>
    <li class="iconSel"><i class="fa fa-apple"></i></li>
    <li class="iconSel"><i class="fa fa-windows"></i></li>
    <li class="iconSel"><i class="fa fa-android"></i></li>
    <li class="iconSel"><i class="fa fa-linux"></i></li>
    <li class="iconSel"><i class="fa fa-dribbble"></i></li>
    <li class="iconSel"><i class="fa fa-trello"></i></li>
    <li class="iconSel"><i class="fa fa-female"></i></li>
    <li class="iconSel"><i class="fa fa-male"></i></li>
    <li class="iconSel"><i class="fa fa-gittip"></i></li>
    <li class="iconSel"><i class="fa fa-sun-o"></i></li>
    <li class="iconSel"><i class="fa fa-moon-o"></i></li>
    <li class="iconSel"><i class="fa fa-archive"></i></li>
    <li class="iconSel"><i class="fa fa-pagelines"></i></li>
    <li class="iconSel"><i class="fa fa-wheelchair"></i></li>
</div>
<script>
    $('.iconSel').on('click', function() {
        $('.iconSelected').removeClass('iconSelected');
        $(this).addClass('iconSelected');
    });
    $('.iconSel').on('dblclick', function() {
        $('.iconSelected').removeClass('iconSelected');
        $(this).addClass('iconSelected');
        $('#mod_selectIcon').dialog("option", "buttons")['Valider'].apply($('#mod_selectIcon'));
    });
</script>
