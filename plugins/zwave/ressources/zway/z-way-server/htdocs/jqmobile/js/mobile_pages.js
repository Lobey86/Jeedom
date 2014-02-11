// create pages

var parameterSendTimer; // timer to send slider operations with small delay
function parameterSend(device, instance, commandclass, method, val) {
	if (parameterSendTimer)
		clearTimeout(parameterSendTimer);

	parameterSendTimer=setTimeout(function(){
		runCmd('devices[' + device + '].instances[' + instance + ']' + '.commandClasses[' + parseInt(commandclass).toString() + ']' + '.' + method + '(' + val + ')');
	}, 200);
}

var areas;
function initDashboard() {
	areas = parseAreas(config.areas);

	renderActors($('#lights_content'), null, 'light');
	renderActors($('#blinds_content'), null, 'blind');
	renderDeviceInfo($('#deviceinfo_content'));
	renderBatteries($('#batteries_content'));
	renderRooms($('#rooms_content'));
	renderSensors($('#sensors_content'));
};
 	
// ====== Page Renderers ======

function renderRooms(parent) {
	$.each(areas, function (aId, area) {	
		if (area) {
			var cont = $('<div data-role="collapsible"><h3>' + area + '</h3><p></p></div>');
			var p = cont.find('p');
			renderActors(p, aId, null);
			renderSensors(p, aId, null);
 		};
		parent.append(cont);
	});
};

function renderActors(parent, areaId, filter) {
	$.each(ZWaveAPIData['devices'], function (nodeId, dev) {
		if (nodeId != 255 && nodeId != ZWaveAPIData.controller.data.nodeId.value && (!areaId || isInArea(nodeId, areaId)))
			$.each(dev['instances'], function (instanceId, instance) {
				if (instance['commandClasses'][38]) { // check for multilevel first
					if (dev.data.specificType.value >= 3 && dev.data.specificType.value != 4 && (!filter || filter == 'blind'))
						renderBlindEntry(parent, nodeId, instanceId, 38);
					else if ((dev.data.specificType.value < 3 || dev.data.specificType.value == 4) && (!filter || filter == 'light'))
						renderDimmerEntry(parent, nodeId, instanceId, 38);
				} else if (instance['commandClasses'][37] && (!filter || filter == 'light')) // if no multilevel, check for binary
					renderSwitchEntry(parent, nodeId, instanceId, 37);
			});
	});
};

function renderSensors(parent, areaId, filter) {
	$.each(ZWaveAPIData['devices'], function (nodeId, dev) {
		if (nodeId != 255 && nodeId != ZWaveAPIData.controller.data.nodeId.value && (!areaId || isInArea(nodeId,areaId)))
			$.each(dev['instances'], function (instanceId, instance) {	
				$.each(instance['commandClasses'], function (ccId, cc) {
					if (ccId == 48)
						 $.each(cc.data, function(key, sensor_type) {
							var sensorId = parseInt(key, 10);
							if (isNaN(sensorId) || !cc.data[sensorId])
		                                                return; // not a scale
							renderBinarySensor(parent, nodeId, instanceId, ccId, cc.data, sensorId);
						});
					if (ccId == 49) 
						 $.each(cc.data, function(key, sensor_type) {
							var sensorId = parseInt(key, 10);
							if (isNaN(sensorId) || !cc.data[sensorId])
		                                                return; // not a scale
							renderMultilevelSensor(parent, nodeId, instanceId, ccId, cc.data, sensorId);
						});

					if (ccId == 50) 
						 $.each(cc.data, function(key, scale_val) {
							var scaleId = parseInt(key, 10);
							if (isNaN(scaleId) || !cc.data[scaleId])
								return; // not a scale
							renderMeter(parent, nodeId, instanceId, ccId, cc.data, scaleId);
						});
				});
			});
	});
};

function renderBinarySensor(parent, nodeId, instanceId, ccId, ccData, sensor) {
	var row = $('<div data-role="collapsible"><h3>' + getNodeName(nodeId,instanceId) + ': ' + ccData[sensor].sensorTypeString.value + ' '  + $.translate('is') + ' ' + '<span id="val"></span></h3>' +
		'<div data-role="controlgroup" data-type="horizontal">' +
			'<a href="#" onmousedown="runCmd(\'devices[' + nodeId + '].instances[' + instanceId + '].commandClasses[' + ccId + '].Get(' + sensor.toString(10) + ')\');" data-role="button" data-icon="refresh">' + $.translate('Refresh') + '</a>' + ' ' +
			$.translate('Last updated') + ': <span id="updated"></span>' +
		'</div>' +
		'</div>');
	row.find('#val').bindPath('devices.' + nodeId + '.instances.' + instanceId + '.commandClasses.' + ccId + '.data.' + sensor.toString(10), function(sens) {
		$(this).html(sens.level.value ? 'active / open' : 'inactive / silent ');
		$(this).closest('div').find('#updated').html(getTime(sens.updateTime)).css('color', (sens.updateTime > sens.invalidateTime) ? 'black' : 'red');
	});
	parent.append(row);
};

function renderMultilevelSensor(parent, nodeId, instanceId, ccId, ccData, sensor) {
	var row = $('<div data-role="collapsible"><h3>' + getNodeName(nodeId,instanceId) + ': ' + ccData[sensor].sensorTypeString.value + ' ' + $.translate('is') + ' ' + '<span id="val"></span>' + ccData[sensor].scaleString.value + '</h3>' + 
		'<div data-role="controlgroup" data-type="horizontal">' +
			'<a href="#" onmousedown="runCmd(\'devices[' + nodeId + '].instances[' + instanceId + '].commandClasses[' + ccId + '].Get(' + sensor.toString(10) + ')\');" data-role="button" data-icon="refresh">' + $.translate('Refresh') + '</a>' + ' ' +
			$.translate('Last updated') + ': <span id="updated"></span>' +
		'</div>' +
		'</div>');
	row.find('#val').bindPath('devices.' + nodeId + '.instances.' + instanceId + '.commandClasses.' + ccId + '.data.' + sensor.toString(10), function(sens) {
		$(this).html(sens.val.value);
		$(this).closest('div').find('#updated').html(getTime(sens.updateTime)).css('color', (sens.updateTime > sens.invalidateTime) ? 'black' : 'red');
	});
	parent.append(row);
};

function renderMeter(parent, nodeId, instanceId, ccId, ccData, scale) {
	var row = $('<div data-role="collapsible"><h3>' + getNodeName(nodeId,instanceId) + ': ' + ccData[scale].sensorTypeString.value + ' ' + $.translate('is') + ' ' + '<span id="val"></span>' + ccData[scale].scaleString.value + '</h3>' + 
		'<div data-role="controlgroup" data-type="horizontal">' +
			'<a href="#" onmousedown="runCmd(\'devices[' + nodeId + '].instances[' + instanceId + '].commandClasses[' + ccId + '].Get(' + scale.toString(10) + ')\');" data-role="button" data-icon="refresh">' + $.translate('Refresh') + '</a>' + ' ' +
			$.translate('Last updated') + ': <span id="updated"></span>' +
		'</div>' +
		'</div>');
	row.find('#val').bindPath('devices.' + nodeId + '.instances.' + instanceId + '.commandClasses.' + ccId + '.data.' + scale.toString(10), function(sc) {
		$(this).html(sc.val.value.toString(10));
		$(this).closest('div').find('#updated').html(getTime(sc.updateTime)).css('color', (sc.updateTime > sc.invalidateTime) ? 'black' : 'red');
	});
	parent.append(row);
};

function renderBlindEntry(parent, nodeId, instanceId, ccData) {
	var row = $(
		'<div data-role="collapsible" data-collapsed="true"><h3><img id="val_img"/> ' + getNodeName(nodeId, instanceId) + ' ' + $.translate('is') + ' <span id="val"></span></h3>' +
		'<div data-role="controlgroup" data-type="horizontal">' +
			'<a href="#" onmousedown="runCmd(\'devices[' + nodeId + '].instances[' + instanceId + '].SwitchMultilevel.StartLevelChange(False)\');"  data-role="button" data-icon="arrow-d">' + $.translate('Close') + '</a>' +
			'<a href="#" onmousedown="runCmd(\'devices[' + nodeId + '].instances[' + instanceId + '].SwitchMultilevel.StartLevelChange(True)\');"  data-role="button" data-icon="arrow-u">' + $.translate('Open') + '</a>' +
			'<a href="#" onmousedown="runCmd(\'devices[' + nodeId + '].instances[' + instanceId + '].SwitchMultilevel.StopLevelChange()\');"  data-role="button" data-icon="minus">' + $.translate('Stop') + '</a>' +
			'<a href="#" onmousedown="runCmd(\'devices[' + nodeId + '].instances[' + instanceId + '].SwitchMultilevel.Get()\');" data-role="button" data-icon="refresh">' + $.translate('Refresh') + '</a><br/>' + 
			$.translate('Last updated') + ': <span id="updated"></span>' +
		'</div>' +
		'<div data-role="fieldcontain" class="ui-hide-label">' +
			'<label for="slider"></label>' +
			'<input type="range" name="slider" id="slider" value="0" min="0" max="99"/>' +
		'</div>' +
		'</div>');

	row.find('#val').bindPath('devices.' + nodeId + '.instances.' + instanceId + '.commandClasses.38.data.level', function(level, path, devId) {
		$(this).html(level.value == 0 ? $.translate('Closed') : ((level.value == 99 || level.value == 255)? $.translate('Opened') : (level.value + '%')));
		$(this).closest('div').find('#updated').html(getTime(level.updateTime)).css('color', (level.updateTime > level.invalidateTime) ? 'black' : 'red');
		$(this).closest('div').find('#val_img').attr('src', getDeviceIcon(devId));
	}, nodeId);
	parent.append(row);

	(function(el, device, instance, commandClass, method) {
		el.delegate('#slider', 'change', function() {
			parameterSend(device, instance, commandClass, method, $(this).val());
		});
	})(row, nodeId, instanceId, 38, 'Set');
};


function renderDimmerEntry(parent, nodeId, instanceId, ccData) {
	var row = $(
		'<div data-role="collapsible" data-collapsed="true"><h3><img id="val_img"/> ' + getNodeName(nodeId, instanceId) + ' ' + $.translate('is') + ' <span id="val"></span></h3>' +
		'<div data-role="controlgroup" data-type="horizontal">' +
			'<a href="#" onmousedown="runCmd(\'devices[' + nodeId + '].instances[' + instanceId + '].SwitchMultilevel.Set(0)\');"  data-role="button" data-icon="arrow-d">Off</a>' +
			'<a href="#" onmousedown="runCmd(\'devices[' + nodeId + '].instances[' + instanceId + '].SwitchMultilevel.Set(255)\');" data-role="button" data-icon="arrow-u">On</a>' +
			'<a href="#" onmousedown="runCmd(\'devices[' + nodeId + '].instances[' + instanceId + '].SwitchMultilevel.Set(99)\');"  data-role="button" data-icon="arrow-d">Full</a>' +
			'<a href="#" onmousedown="runCmd(\'devices[' + nodeId + '].instances[' + instanceId + '].SwitchMultilevel.Get()\');" data-role="button" data-icon="refresh">' + $.translate('Refresh') + '</a><br/>' + 
			$.translate('Last updated') + ': <span id="updated"></span>' +
		'</div>' +
		'<div data-role="fieldcontain" class="ui-hide-label">' +
			'<label for="slider"></label>' +
			'<input type="range" name="slider" id="slider" value="0" min="0" max="99"/>' +
		'</div>' +
		'</div>');

	row.find('#val').bindPath('devices.' + nodeId + '.instances.' + instanceId + '.commandClasses.38.data.level', function(level, path, devId) {
		$(this).html(level.value == 0 ? $.translate('Off') : ((level.value == 99 || level.value == 255)? $.translate('On') : (level.value + '%')));
		$(this).closest('div').find('#updated').html(getTime(level.updateTime)).css('color', (level.updateTime > level.invalidateTime) ? 'black' : 'red');
		$(this).closest('div').find('#val_img').attr('src', getDeviceIcon(devId));
	}, nodeId);
	//getBinaryImage(ccData.level.value)
	parent.append(row);

	(function(el, device, instance, commandClass, method) {
		el.delegate('#slider', 'change', function() {
			parameterSend(device, instance, commandClass, method, $(this).val());
		});
	})(row, nodeId, instanceId, 38, 'Set');
};

function renderSwitchEntry(parent, nodeId, instanceId, ccData) {
	var row = $('<div data-role="collapsible" data-collapsed="true"><h3><img id="val_img"/> ' + getNodeName(nodeId, instanceId) + ' ' + $.translate('is') + ' <span id="val"></span></h3>' +
		'<div data-role="controlgroup" data-type="horizontal">' +
			'<a href="#" onmousedown="runCmd(\'devices[' + nodeId + '].instances[' + instanceId + '].SwitchBinary.Set(0)\');"  data-role="button" data-icon="arrow-d">' + $.translate('Off') + '</a>' +
			'<a href="#" onmousedown="runCmd(\'devices[' + nodeId + '].instances[' + instanceId + '].SwitchBinary.Set(255)\');" data-role="button" data-icon="arrow-u">' + $.translate('On') + '</a>' +
			'<a href="#" onmousedown="runCmd(\'devices[' + nodeId + '].instances[' + instanceId + '].SwitchBinary.Get()\');" data-role="button" data-icon="refresh">' + $.translate('Refresh') + '</a><br/>' +
			$.translate('Last updated') + ': <span id="updated"></span>' +
		'</div>' +
		'</div>');
	row.find('#val').bindPath('devices.' + nodeId + '.instances.' + instanceId + '.commandClasses.37.data.level', function(level, path, devId) {
		$(this).html($.translate(level.value ? 'On': 'Off'));
		$(this).closest('div').find('#updated').html(getTime(level.updateTime)).css('color', (level.updateTime > level.invalidateTime) ? 'black' : 'red');
		$(this).closest('div').find('#val_img').attr('src', getDeviceIcon(devId));
	}, nodeId);
	//getBinaryImage(ccData.level.value)
	parent.append(row);
};

// !!! re-write the code below

function renderHeatings() {
	$('#heatings_content').bindPath('devices[*].instances[*].commandClasses.67.data.1', function() { 
	out=""
	$.each(ZWaveAPIData['devices'], function (nodeId, dev) {
		if (nodeId < 255 && nodeId != ZWaveAPIData.controller.data.nodeId.value) 
			$.each(dev['instances'], function (instanceId, instance) {	
				$.each(instance['commandClasses'], function (ccId, cc) {
					if (ccId == 0x43) {// setpoint 					
						out += "<div data-role='collapsible'> <h3>"+getNodeName(nodeId)+" : "				
						out += +cc.data[1].val.value+" "+cc.data[1].scaleString.value
						if (cc.data[1].val.value != cc.data[1].setVal.value)
							out += " > "+cc.data[1].setVal.value+"  "+cc.data[1].scaleString.value								
						out += "</h3><p><b>Device</b>: "+getNodeName(nodeId)+"<br>"
						out += "<b>Current Target</b>: "+cc.data[1].val.value+" "+cc.data[1].scaleString.value+"<br>"
						out +="<b>Set at</b>: "+ getTime(cc.data[1].val.updateTime)+"<br>"
						if (cc.data[1].val.value != cc.data[1].setVal.value)
							out += "<b>New Target</b>: "+cc.data[1].setVal.value+" "+cc.data[1].scaleString.value+"<br>"
						out +="<b>Controlled by</b>: "+ getThermostatMode(instance)+"<br>"
						out += "<div data-role='controlgroup' data-type='horizontal'> "
						out += "<a href='#' data-role='button' data-icon='delete' onmousedown=runCmd('devices["+nodeId+"].instances["+instanceId+"].ThermostatSetPoint.Set(1,5)')>Off</a>"
						out += "<a href='#' data-role='button' data-icon='star' onmousedown='renderHeatSlider("+nodeId+",0,20)'>Manuell</a>"
						out += "<a href='#' data-role='button' data-icon='gear' onmousedown=$('#heatslider_"+nodeId+"').html('')>Automated</a>"
						out += "</div><div id= heatslider_"+nodeId+"></div>"
						out +="</p></div>"
 						}
				})	
			})	
	})
	$(this).html(out)
//	$.triggerPath.debug(true)
	$(this).trigger('create') })}
 

function renderHeatSlider(nodeId,instanceId,val) {
	out =  "<div data-role='fieldcontain'>"
	out += "<input type='range' name='slider"+nodeId+"' " 
	out += "id='slider_"+nodeId+"' value='"+val+"' min='0' max='100' /></div>"	
	$("#heatslider_"+nodeId).html(out)
	$("#heatslider_"+nodeId).trigger('create') 
}

function renderClimateSelection(nodeId,instanceId,val) {
	out =  "<div data-role='fieldcontain'>"
	out += "<input type='range' name='slider"+nodeId+"' " 
	out += "id='slider_"+nodeId+"' value='"+val+"' min='0' max='100' /></div>"	
	$("#heatslider_"+nodeId).html(out)
	$("#heatslider_"+nodeId).trigger('create') 
}
///****//


function renderDeviceInfo(parent) {
	$.each(ZWaveAPIData['devices'], function (nodeId, dev) {
		if (nodeId != 255 && nodeId != ZWaveAPIData.controller.data.nodeId.value) {
			var row = $('<div data-role="collapsible" data-collapsed="true"><h3>' + getNodeName(nodeId) + '</h3>' +
				'<div data-role="controlgroup" data-type="horizontal">' +
					'<label>' + $.translate('Area') + '</label>: ' + getNodeArea(nodeId) + '<br/>' +
					'<label>' + $.translate('Vendor') + '</label>: ' + dev.data.vendorString.value + '<br/>' +
					'<label>' + $.translate('Product name') + '</label>: ' + /*$(dev.data.ZDDXML.value).find("productName").text() + " " + $(dev.data.ZDDXML.value).find("productCode").text()*/ '' + '<br/>' +
					'<label>' + $.translate('Device Type') + '</label>: ' + dev.data.deviceTypeString.value + '<br/>' +
				'</div>' +
				'</div>');
			parent.append(row);
		}
	});
};
 
function renderBatteries(parent) {
	$.each(ZWaveAPIData['devices'], function (nodeId, dev) {
		if (nodeId != 255 && nodeId != ZWaveAPIData.controller.data.nodeId.value && 128 in dev.instances[0].commandClasses) {
			var nbatt = ""; /*parseInt($(dev.data.ZDDXML.value).find("batterycount").text(), 10); */
			var tbatt = ""; /*$(dev.data.ZDDXML.value).find("BATTERYTYPE").text();*/
			var row = $('<div data-role="collapsible" data-collapsed="true"><h3><img id="val_img"/> ' + getNodeName(nodeId) + ' ' + ' <span id="val"></span></h3>' +
				'<div data-role="controlgroup" data-type="horizontal">' +
					(nbatt != 0 ? ('<label>Battery Type</label>: ' + nbatt + ' * ' + tbatt + '<br/>') : '') +
					$.translate('Last updated') + ': <span id="updated"></span>' +
					'<a href="#" onmousedown="runCmd(\'devices[' + nodeId + '].instances[0].Battery.Get()\');" data-role="button" data-icon="refresh">' + $.translate('Refresh') + '</a><br/>' +
				'</div>' +
				'</div>');
			row.find('#val').bindPath('devices.' + nodeId + '.instances.0.commandClasses.128.data.last', function(level) {
				$(this).html(level.value.toString() + '%');
				$(this).closest('div').find('#updated').html(getTime(level.updateTime)).css('color', (level.updateTime > level.invalidateTime) ? 'black' : 'red');
				$(this).closest('div').find('#val_img').attr('src', getBatteryIcon(level.value));
			});
			//getImage(ccData.level.value)
			parent.append(row);
		}
	});
};

// =========== Misc functions ===========

//++
// attach slider event
function attachSliderEvents(slider, callback) {
	slider.siblings('.ui-slider').bind('tap', function() { callback.apply($(this).siblings('input')); });
	slider.siblings('.ui-slider a').bind('taphold', function() { callback.apply($(this).siblings('input')); });
	slider.bind('blur change keyup', function() { callback.apply($(this)); });
};

//++
function isInArea(nodeId, aId) {
	var r = false;
	$.each(config.devices._children, function (dId, device) {
 		if (device)
			if (parseInt(device.getAttribute("area"), 10) == parseInt(aId, 10) && parseInt(device.getAttribute("device"), 10) == parseInt(nodeId)) {
				r = true;
				return false;
			}
	});
	return r;
};

//++
function getTagName(data){
 	for (var i = 0; i < data._children.length; i++) {
		var ch = data._children[i];
		if (ch.tagName.toLowerCase() != "description") {
			error_msg('syntax_error');
			continue;
		};
		return ch.textContent;
	};
	return '?';
};

function getNodeName(nodeId, instanceId) {
	var ii = "";

	if (instanceId > 0)
		ii = " (" + instanceId + ")";

	for (var i = 0; i < config.devices._children.length; i++) {
		if (config.devices._children[i].getAttribute('device') == nodeId)
			return config.devices._children[i].getAttribute('description') + ii;
		}
	return $.translate('Device') + ' ' + nodeId + ii;
};

// returns the deepness level of the area
function area_level(id) {
	var area_list = $(config.areas).find('area[id='+id+']');
	if (area_list.size()==1) {
		return area_list.parents('area').size();
	}

	function parentArea_count(area) {
		var parent_list=$(area).parents('area');
		var count=parent_list.size();
		if (count)
			count+=parentArea_count(parent_list[count-1]);
		return count;
	}

	var max=0;
	for(var i=0; i<area_list.size(); ++i) {	
		var count=parentArea_count(area_list[i]);
		if (count>max)
			max=count;
	}
	return max;
};

function getNodeArea(nodeId) {
        var l_max = 0;
        var sel_areaId = null;

	for (var i = 0; i < config.devices._children.length; i++)
		if (config.devices._children[i].getAttribute('device') == nodeId) {
	                var areaId = config.devices._children[i].getAttribute('area');
        	        if (areaId && area_level(areaId) >= l_max)
                	        sel_areaId = areaId;
		}
	return sel_areaId === null ? $.translate('Undefined') : areas[sel_areaId];
};

function getSceneNameById(aId){
	for (var i = 0; i < config.scenes._children.length; i++) {
		var scene = config.scenes._children[i];
		if (aId == scene.getAttribute("id")) 
			return getTagName(scene);
	};
	return '<span style="color: gray">' + $.translate('undefined') + '</span>';
};

function parseAreas(data, areas) {
	if (!areas)
		var areas = [];

	areas[data.getAttribute("id")] = data.getAttribute("name")
 	for (var i=0; i<data._children.length; i++) {
		var area = data._children[i];
		if (area.tagName.toLowerCase() != "area") {
			error_msg('syntax_error');
			continue;
		};
		parseAreas(area, areas);
	};
	return areas;
};

//!!
function isPowerSensor(data) {
	return (data != undefined) && $.inArray(data.sensorType.value, [4, 15, 16]);
};

//!!
function isTemperatureSensor(data) {
	return (data != undefined) && $.inArray(data.sensorType.value, [4,15,16]);
};

//!!
function isMeter(data) {
	return (data != undefined) && !(data.sensorType.value == 1 && data.scale.value == 2);
};

function getDeviceIcon(icon_nodeId) {
	var icon_name_suffix = 'unregistered';
	var extension = 'png'; // default - can be changed to gif to for animated icons
	if (icon_nodeId == 255)
		icon_name_suffix = 'broadcast';
	else if (icon_nodeId in ZWaveAPIData.devices) {
		var genericType = ZWaveAPIData.devices[icon_nodeId].data.genericType.value;
		var specificType = ZWaveAPIData.devices[icon_nodeId].data.specificType.value;
		icon_name_suffix = genericType + '_' + specificType;
		switch (genericType) {
			case 0x08:
				/* some condition to get thermostat mode
				if (0x in ZWaveAPIData.devices[icon_nodeId].instances[0].commandClasses && ZWaveAPIData.devices[icon_nodeId].instances[0].commandClasses[0x].data.level.value)
					icon_name_suffix += '_255';
				else
				_0 = cool
				_1 = warm
				_2 = hot
				*/
				icon_name_suffix += '_0';
				break;

			case 0x09:
				/* some condition to get window blind state
				if (0x in ZWaveAPIData.devices[icon_nodeId].instances[0].commandClasses && ZWaveAPIData.devices[icon_nodeId].instances[0].commandClasses[0x].data.level.value)
					icon_name_suffix += '_255';
				else
				_0 = open
				_50 = intermedium
				_255 = closed
				*/
				icon_name_suffix += '_50';
				break;
				
			case 0x10:
				if (0x25 in ZWaveAPIData.devices[icon_nodeId].instances[0].commandClasses && ZWaveAPIData.devices[icon_nodeId].instances[0].commandClasses[0x25].data.level.value)
					icon_name_suffix += '_255';
				else
					icon_name_suffix += '_0';
				break;

			case 0x11:
				if (0x26 in ZWaveAPIData.devices[icon_nodeId].instances[0].commandClasses && ZWaveAPIData.devices[icon_nodeId].instances[0].commandClasses[0x26].data.level.value > 50)
					icon_name_suffix += '_255';
				else if (0x26 in ZWaveAPIData.devices[icon_nodeId].instances[0].commandClasses && ZWaveAPIData.devices[icon_nodeId].instances[0].commandClasses[0x26].data.level.value > 0)
					icon_name_suffix += '_50';
				else
					icon_name_suffix += '_0';
				break;

			case 0x20:
				if (0x30 in ZWaveAPIData.devices[icon_nodeId].instances[0].commandClasses && ZWaveAPIData.devices[icon_nodeId].instances[0].commandClasses[0x30].data.level.value)
					icon_name_suffix += '_255';
				else
					icon_name_suffix += '_0';
				break;

			/* SensorMultilevel is represented by one icon for all states
			case 0x21:
				if (0x31 in ZWaveAPIData.devices[icon_nodeId].instances[0].commandClasses && ZWaveAPIData.devices[icon_nodeId].instances[0].commandClasses[0x31].data.val.value)
					icon_name_suffix += '_255';
				break;
			*/

			case 0x40:
				/* some condition to get alarm
				if (0x in ZWaveAPIData.devices[icon_nodeId].instances[0].commandClasses && ZWaveAPIData.devices[icon_nodeId].instances[0].commandClasses[0x].data.level.value)
					icon_name_suffix += '_255';
				else
				_0 = open
				_1 = closed
				_2 = locked
				*/
				icon_name_suffix += '_1';
				break;

			case 0xA1:
				/* some condition to get alarm
				if (0x in ZWaveAPIData.devices[icon_nodeId].instances[0].commandClasses && ZWaveAPIData.devices[icon_nodeId].instances[0].commandClasses[0x].data.level.value) {
					icon_name_suffix += '_255';
					extension = 'gif'; // animated icon
				} else
				*/
				icon_name_suffix += '_0';
				break;

		}
	};
	return 'pics/icons_mobile/device_icon_' + icon_name_suffix + '.' + extension;
};

//!!
function getThermostatMode(instance) {
	if ( 1 ) { //instance.data.thermostatZoneControlled.value) {
		x = " test";
		return "schedule " + x;
	} else
		return "manually";
};

function getBatteryIcon(val){
	if (val < 15 || val == 255)
		return 'pics/icons_mobile/battery_0.png';
	else if (val < 50)
		return 'pics/icons_mobile/battery_50.png';
	else
		return 'pics/icons_mobile/battery_100.png';
};

// Calculates difference between two dates in days
function days_between(date1, date2) {
	return Math.round(Math.abs(date2.getTime() - date1.getTime()) / (1000 * 60 * 60 * 24));
};

// Return string with date in smart format: "hh:mm" if current day, "hh:mm dd" if this week, "hh:mm dd mmmm" if this year, else "hh:mm dd mmmm yyyy"
function getTime(timestamp, invalidReturn) {
	var d = new Date(parseInt(timestamp, 10)*1000);
	if (timestamp === 0 || isNaN(d.getTime()))
		return invalidReturn

	var cd = new Date();

	var fmt;
	if (days_between(cd, d) < 1 && cd.getDate() == d.getDate()) // this day
		fmt = 'HH:MM';
	else if (days_between(cd, d)  < 7 && ((cd < d) ^ (cd.getDay() >= d.getDay()))) // this week
		fmt = 'dddd HH:MM';
	else if (cd.getFullYear() == d.getFullYear()) // this year
		fmt = 'dddd, d mmmm HH:MM';
	else // one upon a time
		fmt = 'dddd, d mmmm yyyy HH:MM';

	return d.format(fmt);
};
