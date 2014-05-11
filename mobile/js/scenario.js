function initScenario() {
    var scenarios = scenario.all();
    var html = '';
    for (var i in scenarios) {
        if (scenarios[i].isVisible == 1) {
            html += scenario.toHtml(scenarios[i].id, 'mobile');
        }
    }
    $('#div_displayScenario').append(html);
    setTileSize('.scenario');
    $(window).on("orientationchange", function(event) {
        setTileSize('.scenario');
    });
}