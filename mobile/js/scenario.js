function initLocalPage() {
    var scenarios = scenario.all();
    var html = '';
    for (var i in scenarios) {
        html += scenario.toHtml(scenarios[i].id, 'mobile');
    }
    $('#div_displayScenario').append(html);
}