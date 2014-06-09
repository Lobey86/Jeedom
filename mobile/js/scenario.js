function initScenario() {
    jeedom.scenario.toHtml('all', 'mobile', function(htmls) {
        var html = '';
        for (var i in htmls) {
            html += htmls[i];
        }
        $('#div_displayScenario').append(html);
        setTileSize('.scenario');
    })
    $(window).on("orientationchange", function(event) {
        setTileSize('.scenario');
    });
}