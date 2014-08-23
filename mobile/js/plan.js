function initPlan(_planHeader_id) {
    jeedom.plan.getHeader({
        id: _planHeader_id,
        error: function(error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
        },
        success: function(planHeader) {
            $('#div_displayObject').append(planHeader.image);
        }
    });
}


