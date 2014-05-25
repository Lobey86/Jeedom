<?php
if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
include_file('core', 'js.inc', 'php');
?>

<style>
    .iconSel{
        float: left;
        width: 5%;
        float: left;
        height: 30px;
        padding: 10px;
        line-height: 1.4;
        text-align: center;
        border: 1px solid #fff;
        box-sizing: border-box;
        display: list-item;
        list-style: none;
        font-size: 1.2em;
        cursor: pointer;
    }

    .iconSelected{
        background-color: #563d7c;
        color: white;
    }
</style>
<legend>{{Générale}}</legend>
<li class="iconSel"><i class="fa fa-glass" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-music" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-search" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-envelope-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-heart" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-star" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-star-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-user" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-film" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-th-large" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-check" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-times" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-power-off" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-signal" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-cog" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-trash-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-home" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-file-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-clock-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-road" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-download" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-inbox" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-play-circle-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-refresh" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-list-alt" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-lock" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-flag" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-headphones" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-volume-down" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-qrcode" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-barcode" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-tag" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-book" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-print" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-camera" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-video-camera" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-picture-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-pencil" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-map-marker" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-tint" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-pencil-square-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-check-square-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-arrows" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-step-backward" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-fast-backward" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-backward" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-play" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-pause" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-stop" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-forward" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-fast-forward" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-step-forward" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-eject" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-chevron-left" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-chevron-right" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-plus-circle" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-minus-circle" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-times-circle" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-check-circle" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-question-circle" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-info-circle" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-crosshairs" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-times-circle-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-check-circle-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-ban" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-arrow-left" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-arrow-right" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-arrow-up" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-arrow-down" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-plus" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-minus" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-asterisk" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-exclamation-circle" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-gift" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-leaf" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-fire" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-eye" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-eye-slash" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-exclamation-triangle" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-plane" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-calendar" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-random" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-comment" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-magnet" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-chevron-up" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-chevron-down" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-shopping-cart" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-folder" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-folder-open" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-bar-chart-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-key" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-heart-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-sign-out" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-thumb-tack" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-sign-in" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-phone" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-unlock" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-credit-card" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-rss" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-hdd-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-bullhorn" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-bell" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-globe" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-wrench" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-filter" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-briefcase" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-cloud" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-flask" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-scissors" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-paperclip" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-floppy-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-table" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-magic" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-truck" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-money" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-columns" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-envelope" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-gavel" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-tachometer" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-bolt" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-sitemap" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-umbrella" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-lightbulb-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-stethoscope" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-suitcase" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-coffee" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-cutlery" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-building-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-medkit" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-beer" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-h-square" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-desktop" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-laptop" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-tablet" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-mobile" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-circle-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-spinner" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-circle" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-smile-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-frown-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-meh-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-gamepad" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-keyboard-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-terminal" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-location-arrow" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-microphone" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-microphone-slash" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-shield" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-calendar-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-fire-extinguisher" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-rocket" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-anchor" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-unlock-alt" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-compass" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-apple" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-windows" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-android" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-linux" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-dribbble" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-trello" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-female" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-male" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-gittip" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-sun-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-moon-o" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-archive" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-pagelines" style="color:black"></i></li>
<li class="iconSel"><i class="fa fa-wheelchair" style="color:black"></i></li>

<?php
foreach (ls('core/img/icon', '*.svg') as $file) {
    echo '<li class="iconSel"><img src="core/img/icon/' . $file . '" height="20" width="20" /></li>';
}

foreach (ls('core/img/icon', '*') as $dir) {
    if (is_dir('core/img/icon/' . $dir)) {
        echo '<div><legend style="margin-bottom: 0px">{{' . str_replace('/', '', $dir) . '}}</legend>';
        foreach (ls('core/img/icon/' . $dir, '*.svg') as $file) {
            echo '<li class="iconSel"><img src="core/img/icon/' . $dir . '/' . $file . '" height="20" /></li>';
        }
        echo "</div></br/></br/></br/></br/>";
    }
}
?>

<script>
    $('.iconSel').on('click', function() {
        $('.iconSelected').removeClass('iconSelected');
        $(this).addClass('iconSelected');
    });
</script>
