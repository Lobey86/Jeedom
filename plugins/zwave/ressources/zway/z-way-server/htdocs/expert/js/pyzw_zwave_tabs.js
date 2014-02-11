/*
# This file is part of PyZW.
#
# Copyright (C) 2010 Poltorak Serguei
#
# PyZW is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# PyZW is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with PyZW.  If not, see <http://www.gnu.org/licenses/>.
*/

function tabs_zwave_start() {
	$.triggerPath.init(ZWaveAPIData);

	$('button').button();

	$('#nm_include_start').bind("click", function() { runCmd('controller.AddNodeToNetwork(1)'); });
	$('#nm_include_stop').bind("click", function() { runCmd('controller.AddNodeToNetwork(0)'); });
	$('#nm_exclude_start').bind("click", function() { runCmd('controller.RemoveNodeFromNetwork(1)'); });
	$('#nm_exclude_stop').bind("click", function() { runCmd('controller.RemoveNodeFromNetwork(0)'); });
	$('#nm_learn_start').bind("click", function() { runCmd('controller.SetLearnMode(1)'); });
	$('#nm_learn_stop').bind("click", function() { runCmd('controller.SetLearnMode(0)'); });
	$('#nm_controller_change_start').bind("click", function() { runCmd('controller.ControllerChange(1)'); });
	$('#nm_controller_change_stop').bind("click", function() { runCmd('controller.ControllerChange(0)'); });
	$('#nm_remove_failed').bind("click", function() { if ($('#nm_remove_failed_node').val() != null) confirm_dialog($.translate('are_you_sure_remove_node') + ' ' + $('#nm_remove_failed_node option:selected').text() + '?', $.translate('nm_remove_failed'), function() { runCmd('devices[' + $('#nm_remove_failed_node').val() + '].RemoveFailedNode()'); confirm_dialog($.translate('would_you_like_delete_device_config'), $.translate('nm_reset_controller'), function() { delete_device_config($('#nm_remove_failed_node').val()); config_conf_save('devicechange'); }); }); });
	$('#nm_mark_battery_as_failed').bind("click", function() { if ($('#nm_mark_battery_as_failed_node').val() != null) { runCmd('devices[' + $('#nm_mark_battery_as_failed_node').val() + '].SendNoOperation()'); runCmd('devices[' + $('#nm_mark_battery_as_failed_node').val() + '].WakeupQueue()', function() { runCmd('IsFailedNode(' + $('#nm_mark_battery_as_failed_node').val() + ')'); }); }});
	$('#nm_get_suc_nodeid').bind("click", function() { runCmd('controller.GetSUCNodeId()'); });
	$('#nm_request_network_update').bind("click", function() { runCmd('controller.RequestNetworkUpdate()'); });
	$('#nm_start_suc').bind("click", function() { runCmd('controller.SetSUCNodeId(' + $('#nm_suc_node').val() + ')'); });
	$('#nm_start_sis').bind("click", function() { runCmd('controller.SetSISNodeId(' + $('#nm_suc_node').val() + ')'); });
	$('#nm_stop_suc_sis').bind("click", function() { runCmd('controller.DisableSUCNodeId(' + $('#nm_suc_node').val() + ')'); });
	$('#nm_soft_reset_controller').bind("click", function() { runCmd('SerialAPISoftReset()'); });
	$('#nm_reset_controller').bind("click", function() { confirm_dialog($.translate('are_you_sure_reset_controller'), $.translate('nm_reset_controller'), function() { runCmd('controller.SetDefault()'); confirm_dialog($.translate('would_you_like_reset_config'), $.translate('nm_reset_controller'), {'purge_config_devices_only': function() { $(this).dialog('close'); $(config).find('device').remove(); config_save(); $(config_conf).empty(); config_conf_save(); interface_update(); }, 'purge_full_config': function() { $(this).dialog('close'); $(config).empty(); config_save(); $(config_conf).empty(); config_conf_save(); interface_update(); }, 'cancel': function() { $(this).dialog('close');} }); }); });
	//$('#nm_network_healing').bind("click", function() { runCmd('NetworkHealing()'); });
	$('#nm_send_node_information').bind("click", function() { runCmd('controller.SendNodeInformation()'); });
	$('#nm_request_all_node_information').bind("click", function() { for (nodeId in ZWaveAPIData.devices) if (nodeId != ZWaveAPIData.controller.data.nodeId.value && nodeId != 255) runCmd('devices[' + nodeId + '].RequestNodeInformation()'); });
	//$('#halt_server').bind("click", function() { confirm_dialog($.translate('are_you_sure_halt_server'), $.translate('nm_halt_server'), function() { runCmd('Exit()'); }); });
	$('#reset_config_file').bind("click", function() { confirm_dialog($.translate('are_you_sure_reset_config'), $.translate('reset_config_file'), function() { $(config_conf).find('devices').empty(); showConfigUI(); } ); });
	$('#save_devices_data').bind("click", function() { runCmd('devices.SaveData()'); });
	$('#ctrl_info_data').bind("click", function() { showDataHolder(ZWaveAPIData.controller.data); });
	$('#ctrl_info_device_data').bind("click", function() { showDataHolder(ZWaveAPIData.devices[ZWaveAPIData.controller.data.nodeId.value].data); });
	$('#rt_update_nodes_neightbours').bind("click", function() { updateNodesNeighbours(); });
	$('#nm_count_jobs_enable').bind("click", function() { runCmd('controller.data.countJobs.Update(True)'); });
	$('#nm_count_jobs_disable').bind("click", function() { runCmd('controller.data.countJobs.Update(False)'); });
	$('#nm_inspect_queue_title').bind("click", function() { openQueueWindow(); });
	//$('#comm_timing').bind("click", function() { showCommTiming(); });
	$('#nm_restore_backup_upload').bind("click", function() { confirm_dialog($.translate('are_you_sure_restore'), $.translate('restore_backup_upload'), function() { restore_backup(); }); });
};

function showInterviewResults(nodeId) {	
	$('#interview_result')
		.bindPath('devices[' + nodeId + '].instances[*].commandClasses,devices[' + nodeId + '].instances[*].commandClasses[*].data.interviewDone', function() {
			interviewResults = $('<table id="interviewResultsTable"><tr><td>Instance</td><td>Command Class</td><td>Result</td></tr></table>');
			for (var iId in ZWaveAPIData.devices[nodeId].instances)
				for (var ccId in ZWaveAPIData.devices[nodeId].instances[iId].commandClasses) {
					ccResult = $('<tr><td align="center"><a href="#" class="a_instance">' + iId + '</a></td><td><a href="#" class="a_command_class">' + ZWaveAPIData.devices[nodeId].instances[iId].commandClasses[ccId].name + '</a></td><td>' + (ZWaveAPIData.devices[nodeId].instances[iId].commandClasses[ccId].data.interviewDone.value? 'Done': '<button class="run geek"></button>') + '</td></tr>');
					(function(nodeId, iId) {
						ccResult.find('a.a_instance').bind("click", function() { showDataHolder(ZWaveAPIData.devices[nodeId].instances[iId].data); });
					})(nodeId, iId);
					(function(nodeId, iId, ccId) {
						ccResult.find('a.a_command_class').bind("click", function() { showDataHolder(ZWaveAPIData.devices[nodeId].instances[iId].commandClasses[ccId].data); });
						ccResult.find('.run').bind("click", function() { runCmd('devices[' + nodeId + '].instances[' + iId + '].commandClasses[' + ccId + '].Interview()'); }).html($.translate('config_ui_force_interview')).button();
					})(nodeId, iId, ccId);
					interviewResults.append(ccResult);
				}
			 $(this).html($.translate('interview_results_title') + ': <a href="#" class="a_device">' + device_name(nodeId) + '</a><br /><br />').append(interviewResults);
			 $('#interview_result').find('a.a_device').bind("click", function() { showDataHolder(ZWaveAPIData.devices[nodeId].data); });
		})
		.append(interviewResults)
		.css({'max-height': $(document.body).height()-128})
		.dialog({
			modal: true,
		       	title: $.translate('interview_results_dialog_title'),
			width: 'auto',
			open: function() {
				dialog_init(this);
			},
		       	buttons: {
				ok : function() {
					$(this).dialog("close");
				}
			}
		});

};

// this interface is used in area status and thermostat tabs
function temperature_shift_guiInit(spans, options, setClickHandler, getter) {
	var menu_base_id = 'temperature_change_menu';
	var menu_id = menu_base_id + '_' + (options.shiftAbs ? 'shift' : 'absolute') + '_' + (options.min < 0 ? '_' : '') + Math.abs(options.min).toString(10) + '_' + (options.max < 0 ? '_' : '') + Math.abs(options.max).toString(10);

	if ($('#' + menu_id).size() == 0) {
		var menu_base = $('#' + menu_base_id);
		var menu = menu_base.clone().attr('id', menu_id);
		menu_base.after(menu);
		for (var n = options.max; n >= options.min; n--) {
			var itemStr = '';
			if (options.shiftAbs) {
				if (n == 0)
					itemStr = '0';
				else if (n > 0)
					itemStr = '&uarr;' + n.toString(10);
				else
					itemStr = '&darr;' + (-n).toString(10);
			} else
				itemStr = n.toString(10);
			menu.append($('<li value="' + n + '" class="icon"><span class="icon ok"></span><span>' + itemStr + '</span></li>'));
		}
	};
	spans.append($('<button id="temperature_change" class="intl"></button>'))
		.addClass('temperature_shift_gui_span')
		.jeegoocontext(menu_id, {
			onShow: function(event,context) {
				var curVal = getter.call($(context).closest('.temperature_shift_gui_span'));
				$(this).find('> li').find('.icon').css({opacity: 0});
				$(this).find('> li[value="' + curVal + '"]').find('.icon').css({opacity: 100});;
			},
			onSelect: function(event,context) {
				setClickHandler.call($(context).closest('.temperature_shift_gui_span'), $(this).attr('value'));
			}
		})
		.find('#temperature_change').translate().button().bind('mousedown',function(event){event.type='contextmenu';$(this).trigger(event)});
};

function hideDevicesStatus() {
	$('#devicestatus table.devicesTable').empty();
};

function showDevicesStatus() {
	function updateDeviceInfo(obj, path, basicType, genericType, specificType, isFLiRS, hasWakeup, hasBattery, isListening) {
		var nodeId = $(this).attr('device');
		var node = ZWaveAPIData.devices[nodeId];
		var lastReceive = parseInt(node.data.lastReceived.updateTime, 10) || 0;
		var lastSend = parseInt(node.data.lastSend.updateTime, 10) || 0;
		var lastCommunication = (lastSend > lastReceive)? lastSend : lastReceive;
		var isFailed = node.data.isFailed.value;
		var isAwake = node.data.isAwake.value;

		var sleeping_cont;
		if (isListening)
			sleeping_cont = ''; // mains powered device
		else if (!isListening && hasWakeup) {
			var approx = '';
			var sleepingSince = parseInt(node.instances[0].commandClasses[0x84].data.lastSleep.value, 10);
			var lastWakeup = parseInt(node.instances[0].commandClasses[0x84].data.lastWakeup.value, 10);
			if (isNaN(sleepingSince) || sleepingSince < lastWakeup) {
				sleepingSince = lastWakeup
				if (!isNaN(lastWakeup))
					approx = '<span title="' + $.translate('sleeping_since_approximately') + '">~</span> ';
			};
			var interval = parseInt(node.instances[0].commandClasses[0x84].data.interval.value, 10);
			if (interval == 0)
				interval = NaN; // to indicate that interval and hence next wakeup are unknown
			var lastSleep = getTime(sleepingSince, '?');
			var nextWakeup = getTime(sleepingSince + interval, '?');
			sleeping_cont = '<span title="' + $.translate('sleeping_since') + '" class="not_important">' + approx + lastSleep + '</span> &#8594; <span title="' + $.translate('next_wakeup') + '">' + approx + nextWakeup + '</span> <img src="pics/icons/type_battery_with_wakeup.png" title="' + $.translate('battery_operated_device_with_wakeup') + '"/>';
		} else if (!isListening && isFLiRS)
			sleeping_cont = '<img src="pics/icons/type_flirs.png" title="' + $.translate('FLiRS_device') + '"/>';
		else
			sleeping_cont = '<img src="pics/icons/type_remote.png" title="' + $.translate('battery_operated_remote_control') + '"/>';

		var awake_cont = '';
		if (!isListening && !isFLiRS)
			awake_cont = isAwake?('<img src="pics/icons/status_awake.png" title="' + $.translate('device_is_active') + '"/>'):('<img src="pics/icons/status_sleep.png" title="' + $.translate('device_is_sleeping') + '"/>');
			
		var operating_cont = (isFailed?('<img src="pics/icons/status_dead.png" title="' + $.translate('device_is_dead') + '"/>'):('<img src="pics/icons/status_ok.png" title="' + $.translate('device_is_operating') + '"/>')) + ' <span title="' + $.translate('last_communication') + '" class="not_important">' + getTime(lastCommunication, '?') + '</span>';

		var interview_cont = '';
		var _interview_cont = '<a href="#" id="interviewNotFinished"><img src="pics/icons/interview_unfinished.png" title="' + $.translate('device_is_not_fully_interviewed') + '"/></a>';
		if (ZWaveAPIData.devices[nodeId].data.nodeInfoFrame.value && ZWaveAPIData.devices[nodeId].data.nodeInfoFrame.value.length) {
			for (var iId in ZWaveAPIData.devices[nodeId].instances)		
				for (var ccId in ZWaveAPIData.devices[nodeId].instances[iId].commandClasses)
					if (!ZWaveAPIData.devices[nodeId].instances[iId].commandClasses[ccId].data.interviewDone.value)  {
						interview_cont = _interview_cont;
					}
		} else
			interview_cont = _interview_cont;

		var battery_cont = '';
		if (hasBattery) {
			var battery_charge = parseInt(node.instances[0].commandClasses[0x80].data.last.value);
			var battery_updateTime = getTime(node.instances[0].commandClasses[0x80].data.last.updateTime);
			var battery_warn;
			var battery_charge_icon;
			var battery_charge_text;
			if (battery_charge != null) {
				if (battery_charge == 255) // by CC Battery specs
					battery_charge = 0;
				battery_warn = (battery_charge < 10)
				battery_charge_text = battery_charge.toString() + '%';
				battery_charge_icon = (battery_charge < 10) ? '0' : ((battery_charge < 50) ? '50' : '100');
			} else {
				battery_warn = true;
				battery_charge_text = '?';
				battery_charge_icon = '0';
			};
			battery_cont = '<img src="pics/icons/battery_' + battery_charge_icon + '.png" title="' + $.translate('battery_powered_device') + '"/> <span class="' + (battery_warn?'red':'') + '" title="' + battery_updateTime + '">' + battery_charge_text + '</span>';
		};

		$(this).find('#sleeping').html(sleeping_cont);
		$(this).find('#awake').html(awake_cont);
		$(this).find('#operating').html(operating_cont);
		$(this).find('#battery').html(battery_cont);
		$(this).find('#interview').html(interview_cont);
		if (ZWaveAPIData.controller.data.countJobs.value)
			$(this).find('#queue_length').html(node.data.queueLength.value ? node.data.queueLength.value : '');
		else
			$(this).find('#queue_length').html('-');

		if (isListening || isFLiRS)
			$(this).find('#pingDevice').show();
		else
			$(this).find('#pingDevice').hide();
	};
	hideDevicesStatus();
	var tbl = $('#devicestatus table.devicesTable');
	$.each(ZWaveAPIData.devices, function (nodeId, node) {
		var nodeIsVirtual = node.data.isVirtual.value;
		var controllerNodeId = ZWaveAPIData.controller.data.nodeId.value;
		if (nodeId == 255 || nodeId == controllerNodeId || nodeIsVirtual)
			return;
		var basicType = node.data.basicType.value;
		var genericType = node.data.genericType.value;
		var specificType = node.data.specificType.value;
		var isListening = node.data.isListening.value;
		var isFLiRS = !isListening && (node.data.sensor250.value || node.data.sensor1000.value);
		var hasWakeup = 0x84 in node.instances[0].commandClasses;
		var hasBattery = 0x80 in node.instances[0].commandClasses;

		var nodeTr = $('<tr device="' + nodeId + '" class="device_header"><td class="center not_important">' + nodeId + '</td><td class="icon"></td><td>' + device_area_name(nodeId, 'not_important') + '</td><td class="right" id="sleeping"></td><td id="awake"></td><td id="operating"></td><td id="battery"></td><td id="interview"></td><td id="queue_length" title="' + $.translate('device_queue_length') + '"></td><td class="geek"><button id="pingDevice"></button></td></tr>');
		nodeTr.find('td.icon').append(device_icon(nodeId, true));

		var prefixD = 'devices.' + nodeId + '.data.';
		var prefixIC = 'devices.' + nodeId + '.instances[0].commandClasses'
		nodeTr.bindPath(prefixD + 'isFailed,' + prefixD + 'isAwake,' + prefixD + 'lastSend,' + prefixD + 'lastReceived,' + prefixD + 'queueLength,devices.' + nodeId + '.instances[*].commandClasses[*].data.interviewDone,' + prefixIC + '[' + 0x84 + '].data.lastWakeup,' + prefixIC + '[' + 0x84 + '].data.lastSleep,' + prefixIC + '[' + 0x84 + '].data.interval,' + prefixIC + '[' + 0x80 + '].data.last', updateDeviceInfo, basicType, genericType, specificType, isFLiRS, hasWakeup, hasBattery, isListening);

		$('#devicestatus table.devicesTable').append(nodeTr);
	});

	tbl.find('#interviewNotFinished').bind('click', function() { showInterviewResults(parseInt($(this).closest('[device]').attr('device'), 10)); } );
	tbl.find('#pingDevice').bind('click', function() { runCmd('devices[' + parseInt($(this).closest('[device]').attr('device'), 10) + '].SendNoOperation()'); }).html($.translate('pingDevice')).button();

	tbl.find('tbody').first().bindPathNoEval('devices,devices[*].instances,devices[*].instances[*],devices[*].instances[*].commandClasses,devices[*].instances[*].commandClasses[*]', showDevicesStatus);
};

function hideSwitches() {
	$('#switches table.devicesTable').empty();
};

function showSwitches() {
	function updateLevel(obj, path, ccId) {
		var level_cont;
		var level_color;

		var level = obj.value;

		if (level === '' || level === null) {
			level_cont = '?';
			level_color = 'gray';
		} else {
			level = parseInt(level, 10);
			if (level == 0) {
				level_cont = $.translate('switched_off');
				level_color = 'black';
			} else if (level == 255 || level == 99) {
				level_cont = $.translate('switched_on');
				level_color = '#FFCF00';
			} else {
				level_cont = level.toString() + ((ccId == 0x26) ? '%' : '');
				var lvlc_r = ('00' + parseInt(0x9F + 0x60 * level / 99).toString(16)).slice(-2);
				var lvlc_g = ('00' + parseInt(0x7F + 0x50 * level / 99).toString(16)).slice(-2);
				level_color = '#' + lvlc_r + lvlc_g + '00';
			}
		};
		$(this).html(level_cont).css('color', level_color);
		$(this).parent().find('#updateTime').html(getUpdated(obj));
	};

	function updateSwitchall(obj, path, hasSwitchAll) {
		var switchall_cont = '';
		if (hasSwitchAll) {
			switch (parseInt(obj.value, 10)) {
				case 0:
					switchall_cont = '<img src="pics/icons/switch_all_xx_xxx.png" title="' + $.translate('switch_all_no_group') + '"/>';
					break;

				case 1:
					switchall_cont = '<img src="pics/icons/switch_all_xx_off.png" title="' + $.translate('switch_all_off_group') + '"/>';
					break;

				case 2:
					switchall_cont = '<img src="pics/icons/switch_all_on_xxx.png" title="' + $.translate('switch_all_on_group') + '"/>';
					break;

				case 255:
					switchall_cont = '<img src="pics/icons/switch_all_on_off.png" title="' + $.translate('switch_all_on_off_group') + '"/>';
					break;

				default:
					switchall_cont = '?';
			}
		};
		$(this).html(switchall_cont);
	};
	
	hideSwitches();
	var controllerNodeId = ZWaveAPIData.controller.data.nodeId.value;
	$.each(ZWaveAPIData.devices, function (nodeId, node) {
		if (nodeId == 255 || nodeId == controllerNodeId || node.data.isVirtual.value)
			return;
		$.each(node.instances, function(instanceId, instance) {
			if (instanceId == 0 && $.objLen(node.instances) > 1)
				return; // we skip instance 0 if there are more, since it should be mapped to other instances or their superposition
			var hasBinary = 0x25 in instance.commandClasses;
			var hasMultilevel = 0x26 in instance.commandClasses;
			var hasSwitchAll = (0x27 in instance.commandClasses) && (instanceId == 0);
			var ccId;

			if (hasMultilevel)
				ccId = 0x26;
			else if (hasBinary)
				ccId = 0x25;
			else
				return; // we skip instance if there is no SwitchBinary or SwitchMultilevel CCs

			var nodeTr = $('<tr device="' + nodeId + '" class="device_header"><td class="center not_important">' + nodeId + '</td><td class="icon">' + (instanceId != 0?(' (#' + instanceId + ')'):'') + '</td><td>' + device_area_name(nodeId, 'not_important') + '</td><td id="level" class="right"></td><td class="right"><span title="' + $.translate('last_update') + '" id="updateTime"></span></td><td id="switchall"></td><td class="center"><button id="update" class="intl">' + $.translate('update') + '</button></td><td class="right"><span class="value parameter"></span></td></tr>');
			nodeTr.find('td.icon').prepend(device_icon(nodeId, true));

			nodeTr.find('#level').bindPath('devices[' + nodeId + '].instances[' + instanceId + '].commandClasses[' + ccId + '].data.level', updateLevel, ccId);
			nodeTr.find('#switchall').bindPath('devices[' + nodeId + '].instances[' + instanceId + '].commandClasses[' + 0x27 + '].data.mode', updateSwitchall, hasSwitchAll);

			// CC gui
			var lvl = instance.commandClasses[ccId].data.level.value;
			if (lvl === '' || lvl === null)
				lvl = 0;
				
			var custom_type, custom_ui;
			if ((custom_type = $(config.devices).find('[device=' + nodeId + ']').attr('devicetype')) != '' && typeof(device_type_switch_ui) == 'function' && (custom_ui = catch_error(device_type_switch_ui, nodeId, instanceId, custom_type)))
				nodeTr.find('.value.parameter').append(custom_ui);
			else {
				nodeTr.find('.value.parameter').attr('value','[' + lvl + ']');
				method_gui.call(nodeTr.find('.value.parameter').get(0), {
					device: nodeId,
					instance: instanceId,
					commandclass: ccId,
					method: 'Set', // here it is always Set
					methodclass: 'userSet', // here it is always userSet
					immediate: true,
					immediatekeepbutton: false
				});
			}

			(function(nodeId, instanceId, ccId) {
				nodeTr.find('#update').bind('click', function() { runCmd('devices[' + nodeId + '].instances[' + instanceId + '].commandClasses[' + ccId + '].Get()'); } );
			})(nodeId, instanceId, ccId);

			$('#switches table.devicesTable').append(nodeTr);
		});
	});
	
	$('#switches_all_on').bind('click', function() { runCmd('devices[255].instances[0].commandClasses[0x27].SetOn()'); });
	$('#switches_all_off').bind('click', function() { runCmd('devices[255].instances[0].commandClasses[0x27].SetOff()'); });
	$('#switches_update_all').bind('click', function() { UpdateDevicesValues([0x25, 0x26]); } );

	$('#switches table.devicesTable').bindPathNoEval('devices,devices[*].instances,devices[*].instances[*],devices[*].instances[*].commandClasses,devices[*].instances[*].commandClasses[*]', showSwitches);

	$('button').button();
};

function hideSensors() {
	$('#sensors table.devicesTable').empty();
};

function showSensors() {
	function insertRow(nodeId, instanceId, ccId, scaleId, path, updFunc) {
		var nodeTr = $('<tr device="' + nodeId + '" instance="' + instanceId + '" scale="' + scaleId + '" class="device_header"><td class="center not_important">' + nodeId + '</td><td class="icon">' + (instanceId != 0?('(#' + instanceId + ')'):'') + '</td><td>' + device_area_name(nodeId, 'not_important') + '</td><td id="sensor_name"></td><td id="level" class="right"></td><td class="right"><span title="' + $.translate('last_update') + '" id="last_update"></span></td><td class="center"><button id="update" class="intl">' + $.translate('update') + '</button></td></tr>');
		nodeTr.find('td.icon').prepend(device_icon(nodeId, true));

		nodeTr.bindPath('devices[' + nodeId + '].instances[' + instanceId + '].commandClasses[' + ccId + '].data.' + path, updFunc, ccId);

		nodeTr.find('#update').bind('click', function() { runCmd('devices[' + nodeId + '].instances[' + instanceId + '].commandClasses[' + ccId + '].Get()'); } );

		$('#sensors table.devicesTable').append(nodeTr);
	};
	$('#sensors table.devicesTable').empty();
	var controllerNodeId = ZWaveAPIData.controller.data.nodeId.value;
	$.each(ZWaveAPIData.devices, function (nodeId, node) {
		if (nodeId == 255 || nodeId == controllerNodeId || node.data.isVirtual.value)
			return;
		$.each(node.instances, function(instanceId, instance) {
			if (instanceId == 0 && $.objLen(node.instances) > 1)
				return; // we skip instance 0 if there are more, since it should be mapped to other instances or their superposition

			if (0x30 in instance.commandClasses)
				$.each(instance.commandClasses[0x30].data, function(key, sensor_type_val) {
					var sensor_type = parseInt(key, 10);
					if (isNaN(sensor_type))
						return; // not a sensor type
					insertRow(nodeId, instanceId, 0x30, sensor_type, sensor_type, updateSensorMeter);
				});
			
			if (0x31 in instance.commandClasses)
				$.each(instance.commandClasses[0x31].data, function(key, sensor_type_val) {
					var sensor_type = parseInt(key, 10);
					if (isNaN(sensor_type))
						return; // not a sensor type
					insertRow(nodeId, instanceId, 0x31, sensor_type, sensor_type, updateSensorMeter);
				});

			if (0x32 in instance.commandClasses)
				$.each(instance.commandClasses[0x32].data, function(key, scale_val) {
					var scaleId = parseInt(key, 10);
					if (isNaN(scaleId))
						return; // not a scale
					if ((scaleId == 2 || scaleId == 4 || scaleId == 5 || scaleId == 6) && scale_val.sensorType.value == 1)
						insertRow(nodeId, instanceId, 0x32, scaleId, scaleId, updateSensorMeter);
				});
		});
	});
	
	$('#sensors table.devicesTable').bindPathNoEval('devices,devices[*].instances,devices[*].instances[*],devices[*].instances[*].commandClasses,devices[*].instances[*].commandClasses[*]', showSensors);

	$('#sensors_update_all').bind('click', function() { UpdateDevicesValues([0x30, 0x31, 0x32]); } );
	$('button').button();
};

function hideMeters() {
	$('#meters table.devicesTable').empty();
};

// Generates Meters tab
function showMeters() {
	$('#meters table.devicesTable').empty();
	var controllerNodeId = ZWaveAPIData.controller.data.nodeId.value;
	$.each(ZWaveAPIData.devices, function (nodeId, node) {
		if (nodeId == 255 || nodeId == controllerNodeId || node.data.isVirtual.value)
			return;
		$.each(node.instances, function(instanceId, instance) {
			if (instanceId == 0 && $.objLen(node.instances) > 1)
				return; // we skip instance 0 if there are more, since it should be mapped to other instances or their superposition
			if (!(0x32 in instance.commandClasses))
				return; // we don't want devices without meter CC

			$.each(instance.commandClasses[0x32].data, function(key, scale_val) {
				var scaleId = parseInt(key, 10);
				if (isNaN(scaleId))
					return; // not a scale
				if ((scaleId == 2 || scaleId == 4 || scaleId == 5 || scaleId == 6) && scale_val.sensorType.value == 1)
					return; // we don't want to have measurable here (W, V, PowerFactor)

				var nodeTr = $('<tr device="' + nodeId + '" instance="' + instanceId + '" scale="' + scaleId + '" class="device_header"><td class="center not_important">' + nodeId + '</td><td class="icon">' + (instanceId != 0?('(#' + instanceId + ')'):'') + '</td><td>' + device_area_name(nodeId, 'not_important') + '</td><td id="sensor_name"></td><td id="level" class="right"></td><td class="right"><span title="' + $.translate('last_update') + '" id="last_update"></span></td><td class="center"><button id="update" class="intl">' + $.translate('update') + '</button><button id="reset" class="intl">' + $.translate('reset') + '</button></td></tr>');
				nodeTr.find('td.icon').prepend(device_icon(nodeId, true));
				nodeTr.bindPath('devices[' + nodeId + '].instances[' + instanceId + '].commandClasses[' + 0x32 + '].data[' + scaleId + ']', updateSensorMeter);

				if (ZWaveAPIData.devices[nodeId].instances[instanceId].commandClasses[0x32].data.version.value < 2 || !ZWaveAPIData.devices[nodeId].instances[instanceId].commandClasses[0x32].data.resettable.value)
					nodeTr.find('#reset').hide();

				(function(nodeId, instanceId) {
					nodeTr.find('#update').bind('click', function() { runCmd('devices[' + nodeId + '].instances[' + instanceId + '].commandClasses[' + 0x32 + '].Get()'); });
					nodeTr.find('#reset').bind('click', function() { confirm_dialog($.translate('are_you_sure_reset_meter'), $.translate('reset_meter'), function() { runCmd('devices[' + nodeId + '].instances[' + instanceId + '].commandClasses[' + 0x32 + '].Reset()'); }) });
				})(nodeId, instanceId);

				$('#meters table.devicesTable').append(nodeTr);
			});
		});
	});
	
	$('#meters table.devicesTable').bindPathNoEval('devices,devices[*].instances,devices[*].instances[*],devices[*].instances[*].commandClasses,devices[*].instances[*].commandClasses[*]', showMeters);

	$('#meters_update_all').bind('click', function() { UpdateDevicesValues([0x32]); } );
	$('button').button();
};

function hideThermostats() {
	$('#thermostats table.devicesTable').empty();
};

// Returns previous SwitchPoint for current time
function prevSpInSchedule(climateScheduleValue) {
	var curDate = new Date();
	var wDay = curDate.getDay();
	if (wDay == 0)
		wDay = 7;
	var hour = curDate.getHours();
	var minute = curDate.getMinutes();

	if (climateScheduleValue) {
		for (var n = 0; n < 7; n++) {
			var wd = (wDay + 7 - n) % 7;
			if (wd == 0)
				wd = 7;
			for (var spId = 8; spId >= 0; spId--) {
				var sp = climateScheduleValue[wd][spId];
				if (sp.valid.value && ((wd != wDay) || (sp.hour.value < hour || (sp.hour.value == hour && sp.minute.value <= minute)))) {
					return sp;
				}
			}
		}
	}
	return null;
};

// used to pick up thermstat mode
function getCurrentThermostatMode(_instance) {
	var hasThermostatMode = 0x40 in _instance.commandClasses;
	
	var _curThermMode;
	if (hasThermostatMode) {
		_curThermMode = _instance.commandClasses[0x40].data.mode.value;
		if (isNaN(parseInt(_curThermMode, 10)))
			_curThermMode = null; // Mode not retrieved yet
	} else {
		// we pick up first available mode, since not ThermostatMode is supported to change modes
		_curThermMode = null;
		$.each(_instance.commandClasses[0x43].data, function(name) { if (!isNaN(parseInt(name, 10))) { _curThermMode = parseInt(name, 10); return false; } });
	};
	return _curThermMode;
};

// Generates Thermostats tab
function showThermostats() {
	$('#thermostats table.devicesTable').empty();
	var controllerNodeId = ZWaveAPIData.controller.data.nodeId.value;
	$.each(ZWaveAPIData.devices, function (nodeId, node) {
		if (nodeId == 255 || nodeId == controllerNodeId || node.data.isVirtual.value)
			return;
		$.each(node.instances, function(instanceId, instance) {
			if (instanceId == 0 && $.objLen(node.instances) > 1)
				return; // we skip instance 0 if there are more, since it should be mapped to other instances or their superposition

			if (!(0x43 in instance.commandClasses) && !(0x40 in instance.commandClasses))
				return; // we skip devices without ThermostatSetPint AND ThermostatMode CC


			function updateTemp(obj, path, nodeId, instanceId, areaId) {
				var _instance = ZWaveAPIData.devices[nodeId].instances[instanceId];
				var hasThermostatMode = 0x40 in _instance.commandClasses;
				var hasThermostatSetpoint = 0x43 in _instance.commandClasses;
				var hasThermostatSetback = 0x47 in _instance.commandClasses;
				var hasClimateControlSchedule = 0x46 in _instance.commandClasses;

				if (!(hasThermostatSetpoint) && !(hasThermostatMode)) // to include more Thermostat* CCs
					return; // we don't want devices without ThermostatSetpoint AND ThermostatMode CCs

				var curThermMode = getCurrentThermostatMode(_instance);
				var curThermModeName; 
				var curThermModeValid = true;
				if (hasThermostatMode) {
					curThermModeName = (curThermMode in _instance.commandClasses[0x40].data) ? _instance.commandClasses[0x40].data[curThermMode].modeName.value : "???";
					if (curThermMode in _instance.commandClasses[0x40].data) {
						curThermModeValid = _instance.commandClasses[0x40].data.mode.updateTime > _instance.commandClasses[0x40].data.mode.invalidateTime;
					}
				} else {
					curThermModeName = ""; // one mode only, so don't show it
				}

				$(this).find('.curThermMode').html(curThermModeName);

				$(this).find('.control_type').html($.translate('thermostat_controlled_manual'));
				$(this).find('.control_type_switch .to_manual').hide();
				$(this).find('.control_type_switch .to_zone').show();
				$(this).find('.control_type_switch .to_zone').button(areaId ? 'enable' : 'disable');
				if (hasThermostatMode)
					$(this).find('.thermostat_mode_change').show();
				else
					$(this).find('.thermostat_mode_change').hide();
				$(this).find('.temperature_change').show();
				$(this).find('.temperature_shift').hide();

				if (curThermModeValid)
					$(this).find('.curThermMode').removeClass('red');
				else
					$(this).find('.curThermMode').addClass('red');

				if (hasThermostatMode && !_instance.commandClasses[0x43].data[curThermMode]) {
					$(this).find('.curTemp').html('');
					$(this).find('.calcTemp').html('');
					$(this).find('.temperature_change').hide();
					$(this).find('.temperature_shift').hide();
					return; // Mode = Off
				};

				if (curThermMode === null) {
					$(this).find('.curTemp').html('');
					$(this).find('.calcTemp').html('');
					$(this).find('.control_type').html('');
					$(this).find('.temperature_change').hide();
					$(this).find('.temperature_shift').hide();
					$(this).find('.thermostat_mode_change').hide();
					$(this).find('.control_type_switch .to_zone').hide();
					$(this).find('.control_type_switch .to_manual').hide();
					return; // interview is not finished
				};
				
				if (hasThermostatSetpoint && _instance.commandClasses[0x43].data[curThermMode]) {
					var curTempSP = _instance.commandClasses[0x43].data[curThermMode].setVal.value;
					var curTempSPReal = _instance.commandClasses[0x43].data[curThermMode].val.value;
					var curTemp = curTempSP;
					var curTempUnit = _instance.commandClasses[0x43].data[curThermMode].scaleString.value;

					var prevSp = null;
					if (hasClimateControlSchedule)
						prevSp = prevSpInSchedule(_instance.commandClasses[0x46].data.switchpoints);
					if (prevSp !== null) {
						if (prevSp.state.value == 121) {
							curTemp = $.translate("No frost");
							curTempUnit = '';
						} else if (prevSp.state.value == 122) {
							curTemp = $.translate("Energy saving");
							curTempUnit = '';
						} else if (prevSp.state.value < 120)
							curTemp += prevSp.state.value * 0.1;
						else if (prevSp.state.value > 128)
							curTemp += (prevSp.state.value - 256) * 0.1;
						else
							alert_dialog('Unknown value in climate schedule for device ' + nodeId);
					};

					$(this).find('.curTemp').html(curTempSP + ' ' + curTempUnit);
					if (curTempSP == curTempSPReal)
						$(this).find('.curTemp').removeClass('red');
					else
						$(this).find('.curTemp').addClass('red');
				} else {
					$(this).find('.temperature_change').hide();
				};
			};

			var nodeTr = $('<tr device="' + nodeId + '" instance="' + instanceId + '" class="device_header"><td class="center not_important">' + nodeId + '</td><td class="icon">' + (instanceId != 0?('(#' + instanceId + ')'):'') + '</td><td>' + device_area_name(nodeId, 'not_important') + '</td><td class="right curThermMode" title="' + $.translate('current_thermostat_mode') + '"></td><td class="right curTemp" title="' + $.translate('current_temperature') + '"></td><td class="right calcTemp" title="' + $.translate('calculated_temperature') + '"></td><td class="control_mode"><button id="thermostat_mode_change" class="intl thermostat_mode_change"></button></td><td><span class="temperature_change"></span><span class="temperature_shift"></span></td></tr>');
			nodeTr.find('td.icon').prepend(device_icon(nodeId, true));
			nodeTr.find('button').translate().button();

			
			var areaId = device_area(nodeId);
			(function(nId, iId, aId) {
				var ccPath = 'devices[' + nId + '].instances[' + iId + '].commandClasses';
				nodeTr.bindPath(ccPath + '[' + 0x40 + '].data.mode,' + ccPath + '[' + 0x43 + '].data[*].setVal,' + ccPath + '[' + 0x43 + '].data[*],' + ccPath + '[' + 0x46 +'].data.switchpoints.*.*', updateTemp, nId, iId, aId);
			})(nodeId, instanceId, areaId);

			temperature_shift_guiInit(nodeTr.find('.temperature_shift'), {min: -5, max: 5, shiftAbs: true}, function(shift) {
				var devId = $(this).closest('[device]').attr('device');
				var instId = $(this).closest('[device]').attr('instance');
				var area = device_area(devId);

				var _instance = ZWaveAPIData.devices[devId].instances[instId];
				var hasThermostatSetback = 0x47 in _instance.commandClasses;
				var hasClimateControlSchedule = 0x46 in _instance.commandClasses;

				var curThermMode = getCurrentThermostatMode(_instance);
				
				var newTemp = null;
				try {
					if (hasClimateControlSchedule)
						runCmd('devices[' + devId + '].instances[' + instId + '].commandClasses[' + 0x43 + '].Set(' + curThermMode + ', ' + (18 + shift).toString(10) + ')');
					else if (hasThermostatSetback)
						runCmd('devices[' + devId + '].instances[' + instId + '].commandClasses[' + 0x43 + '].Set(' + curThermMode + ', ' + (18 + shift).toString(10) + ')');
					else {
						var prevSp = prevSpInSchedule(_instance.commandClasses[0x43].data[curThermMode].emulatedSchedule);
						if (prevSp !== null)
							runCmd('devices[' + devId + '].instances[' + instId + '].commandClasses[' + 0x43 + '].Set(' + curThermMode + ', ' + (prevSp.absTemp.value + shift).toString(10) + ')');
						else
							alert_dialog($.translate('no_climate_schedule_in_zone'));
					}
				} catch(e) {
					error_msg('error_in_setpoint_value', e);
				}
			}, function() {
				var devId = $(this).closest('[device]').attr('device');
				var instId = $(this).closest('[device]').attr('instance');
				var area = device_area(devId);

				var _instance = ZWaveAPIData.devices[devId].instances[instId];
				var hasThermostatSetback = 0x47 in _instance.commandClasses;
				var hasClimateControlSchedule = 0x46 in _instance.commandClasses;

				var curThermMode = getCurrentThermostatMode(_instance);
				
				if (hasClimateControlSchedule || hasThermostatSetback)
					return _instance.commandClasses[0x43].data[curThermMode].setVal.value - 18;
				else {
					var prevSp = prevSpInSchedule(_instance.commandClasses[0x43].data[curThermMode].emulatedSchedule);
					if (prevSp !== null)
						return _instance.commandClasses[0x43].data[curThermMode].setVal.value - prevSp.absTemp.value;
					else
						return null;
				}
			});

			temperature_shift_guiInit(nodeTr.find('.temperature_change'), {min: 6, max: 30, shiftAbs: false}, function(newTemp) {
				var devId = $(this).closest('[device]').attr('device');
				var instId = $(this).closest('[device]').attr('instance');

				var _instance = ZWaveAPIData.devices[devId].instances[instId];
				var curThermMode = getCurrentThermostatMode(_instance);

				try {
					runCmd('devices[' + devId + '].instances[' + instId + '].commandClasses[' + 0x43 + '].Set(' + curThermMode + ', ' + (newTemp).toString(10) + ')');
				} catch(e) {
					error_msg('error_in_setpoint_value', e);
				}
			}, function() {
				var devId = $(this).closest('[device]').attr('device');
				var instId = $(this).closest('[device]').attr('instance');

				var _instance = ZWaveAPIData.devices[devId].instances[instId];
				var curThermMode = getCurrentThermostatMode(_instance);

				return ZWaveAPIData.devices[devId].instances[instId].commandClasses[0x43].data[curThermMode].setVal.value;
			});

			nodeTr.find('#thermostat_mode_change').
				jeegoocontext('thermostat_mode_change_list', {
					onShow: function(event,context) {
						var devId = parseInt($(context).closest('.device_header').attr('device'), 10);
						var instId = parseInt($(context).closest('.device_header').attr('instance'), 10);
						var _instance = ZWaveAPIData.devices[devId].instances[instId];
						var curThermMode = getCurrentThermostatMode(_instance);
						
						$(this).find('li').each(function() {
							var mode = parseInt($(this).attr('mode'), 10);
							if (mode in _instance.commandClasses[0x40].data) {
								$(this).find('.modename').html(_instance.commandClasses[0x40].data[mode].modeName.value);
								$(this).show();
							} else {
								$(this).hide();
							}
						});
						$(this).find('> li').find('.icon').css({opacity: 0});
						$(this).find('> li[mode="' + curThermMode + '"]').find('.icon').css({opacity: 100});
					},
					onSelect: function(event,context) {
						var devId = parseInt($(context).closest('.device_header').attr('device'), 10);
						var instId = parseInt($(context).closest('.device_header').attr('instance'), 10);
						
						runCmd('devices[' + devId + '].instances[' + instId + '].commandClasses[0x40].Set(' + $(this).attr('mode') + ')');
					}
				})
				.bind('mousedown',function(event){event.type='contextmenu';$(this).trigger(event)});


			$('#thermostats table.devicesTable').append(nodeTr);
		});
	});

	$('#thermostats table.devicesTable').bindPathNoEval('devices,devices[*].instances,devices[*].instances[*],devices[*].instances[*].commandClasses,devices[*].instances[*].commandClasses[*]', showThermostats);
};

function hideLocks() {
	$('#locks table.devicesTable').empty();
};

function showLocks() {
	function updateState(obj, path) {
		var mode = obj.value;
		var mode_lbl;

		if (mode === '' || mode === null) {
			mode_lbl = '?';
		} else {
			switch(mode) {
				case 0x00:
					mode_lbl = 'Open';
					break;
				case 0x10:
					mode_lbl = 'Open from inside';
					break;
				case 0x20:
					mode_lbl = 'Open from outside';
					break;
				case 0xff:
					mode_lbl = 'Closed';
					break;
			};
		};
		$(this).html(mode_lbl);
		$(this).parent().find('#updateTime').html(getUpdated(obj));
	};

	hideLocks();
	var doorLockCCId = 0x62;
	$.each(ZWaveAPIData.devices, function (nodeId, node) {
		if (node.data.isVirtual.value)
			return;
		$.each(node.instances, function(instanceId, instance) {
			if (instanceId == 0 && $.objLen(node.instances) > 1)
				return; // we skip instance 0 if there are more, since it should be mapped to other instances or their superposition

			if (!(doorLockCCId in instance.commandClasses))
				return; // we don't want devices without DoorLock CC

			var nodeTr = $('<tr device="' + nodeId + '" class="device_header"><td class="center not_important">' + nodeId + '</td><td class="icon">' + (instanceId != 0?(' (#' + instanceId + ')'):'') + '</td><td>' + device_area_name(nodeId, 'not_important') + '</td><td id="state" class="right"></td><td class="right"><span title="' + $.translate('last_update') + '" id="updateTime"></span></td><td class="center"><button id="update" class="intl">' + $.translate('update') + '</button></td><td class="right"><span class="value parameter"></span></td></tr>');
			nodeTr.find('td.icon').prepend(device_icon(nodeId, true));

			nodeTr.find('#state').bindPath('devices[' + nodeId + '].instances[' + instanceId + '].commandClasses[' + doorLockCCId + '].data.mode', updateState);

			// CC gui
			var mode = instance.commandClasses[doorLockCCId].data.mode.value;
			if (mode === '' || mode === null)
				mode = 0;
				
			nodeTr.find('.value.parameter').attr('value','[' + mode + ']');
			method_gui.call(nodeTr.find('.value.parameter').get(0), {
				device: nodeId,
				instance: instanceId,
				commandclass: doorLockCCId,
				method: 'Set', // here it is always Set
				methodclass: 'userSet', // here it is always userSet
				immediate: true,
				immediatekeepbutton: false
			});

			(function(nodeId, instanceId) {
				nodeTr.find('#update').bind('click', function() { runCmd('devices[' + nodeId + '].instances[' + instanceId + '].commandClasses[' + doorLockCCId + '].Get()'); } );
			})(nodeId, instanceId);

			$('#locks table.devicesTable').append(nodeTr);
		});
	});
	
	$('#locks table.devicesTable').bindPathNoEval('devices,devices[*].instances,devices[*].instances[*],devices[*].instances[*].commandClasses,devices[*].instances[*].commandClasses[*]', showLocks);

	$('button').button();
};

/*
	Updates levels of all switches and dimmers / sensors / meters / thermostats
*/
function UpdateDevicesValues(ccList) {
	var controllerNodeId = ZWaveAPIData.controller.data.nodeId.value;
	$.each(ZWaveAPIData.devices, function (nodeId, node) {
		if (nodeId == 255 || nodeId == controllerNodeId)
			return;
		$.each(node.instances, function (instanceId, instance) {
			if (instanceId == 0 && $.objLen(node.instances) > 1)
				return; // we skip instance 0 if there are more, since it should be mapped to other instances or their superposition
			for (var ccInd in ccList)
				if (ccList[ccInd] in instance.commandClasses)
					runCmd('devices['+ nodeId +'].instances['+instanceId+'].commandClasses[' + ccList[ccInd] + '].Get()');
		});
	});
};

/*
	Binding function for meters and sensors update
*/
function updateSensorMeter(obj, path, ccId) {
	var level_cont;
	var level_color = 'black';

	var sensorName = obj.sensorTypeString.value;
	updatedTime = getUpdated(obj);
	if (ccId == 0x30) {
		var level = obj.level.value;
		if (level === '' || level === null) {
			level_cont = '?';
			level_color = 'gray';
		} else {
			level_cont = $.translate(level ? 'sensor_triggered' : 'sensor_idle');
			level_color = level ? '#FFCF00' : 'black';
		}
	} else {
		var scale = obj.scaleString.value;
		var val = obj.val.value;
		if (val === '' || val === null) {
			level_cont = '?';
			level_color = 'gray';
		} else
			level_cont = val + ' ' + scale;
	}
	$(this).find('#sensor_name').html(sensorName);
	$(this).find('#level').html(level_cont).css('color', level_color);
	$(this).find('#last_update').html(updatedTime);
};

/*
	Immediate commands or show values in device's context menu.
	Inserts control elements of shows values for each instance.
*/

function createDeviceContextControls(deviceId, menu) {
	function insertSensorRow(nodeId, instanceId, ccId, scaleId, path, updFunc) {
		return $('<li device="' + nodeId + '" instance="' + instanceId + '"><span id="sensor_name"></span><span>' + (instanceId != 0?(' (#' + instanceId + ')'):'') + '</span>: <span id="level"></span></li>')
				.bindPath('devices[' + nodeId + '].instances[' + instanceId + '].commandClasses[' + ccId + '].data.' + path, updFunc, ccId);
	};

	menu = $(menu).empty().append($('<li></li>').addClass('device_name').html(device_name(deviceId, { withoutId: true })));
	var node = ZWaveAPIData.devices[deviceId];
	$.each(node.instances, function(instanceId, instance) {
		if (instanceId == 0 && $.objLen(node.instances) > 1)
			return; // we skip instance 0 if there are more, since it should be mapped to other instances or their superposition

		var commandClassId = null;
		if (0x26 in instance.commandClasses) // SwitchMultilevel
			commandClassId = 0x26;
		else if (0x25 in instance.commandClasses) // SwitchBinary
			commandClassId = 0x25;

		if (commandClassId) { // SwitchBinary or SwitchMultilevel
			var lvl = instance.commandClasses[commandClassId].data.level.value;
			if (lvl === '' || lvl === null)
				lvl = 0;
			var span = $('<span></span>').addClass('value parameter').attr('value','[' + lvl + ']');
			$('<li></li>').append($('<span></span>').addClass('subtagname')).append(span).appendTo(menu);
			method_gui.call(span.get(0), { 
				device: deviceId,
				instance: instanceId,
				commandclass: commandClassId,
				method: 'Set',
				methodclass: 'userSet',
				immediate: true,
				immediatekeepbutton: false
			});

			span.find('button').button();
		};

		if (0x30 in instance.commandClasses) // SensorBinary
			$.each(instance.commandClasses[0x30].data, function(key, type_val) {
				var sensor_type = parseInt(key, 10);
				if (isNaN(sensor_type))
					return; // not a type
				insertSensorRow(deviceId, instanceId, 0x30, sensor_type, sensor_type, updateSensorMeter).appendTo(menu);
			});

		if (0x31 in instance.commandClasses) // SensorMultilevel
			$.each(instance.commandClasses[0x31].data, function(key, type_val) {
				var sensor_type = parseInt(key, 10);
				if (isNaN(sensor_type))
					return; // not a type
				insertSensorRow(deviceId, instanceId, 0x31, sensor_type, sensor_type, updateSensorMeter).appendTo(menu);
			});
		
		if (0x32 in instance.commandClasses) // Meter
			$.each(instance.commandClasses[0x32].data, function(key, scale_val) {
				var scaleId = parseInt(key, 10);
				if (isNaN(scaleId))
					return; // not a scale
				insertSensorRow(deviceId, instanceId, 0x32, scaleId, scaleId, updateSensorMeter).appendTo(menu);
			});

		if (0x43 in instance.commandClasses) { // ThermostatSetPoint
			var hasThermostatMode = 0x40 in instance.commandClasses;
			var curThermMode = getCurrentThermostatMode(instance);
			var curThermModeName;
			if (hasThermostatMode)
					curThermModeName = (curThermMode in instance.commandClasses[0x40].data) ? instance.commandClasses[0x40].data[curThermMode].modeName.value : "???";
				else
					curThermModeName = ""; // one mode only, so don't show it
			
			var curTemp = '';
			var curTempUnit = '';
			if (curThermMode in instance.commandClasses[0x43].data) {
				var curTemp = instance.commandClasses[0x43].data[curThermMode].setVal.value;
				var curTempUnit = instance.commandClasses[0x43].data[curThermMode].scaleString.value;

				var prevSp = null;
				if (0x46 in instance.commandClasses) // ClimateControlSchedule
					prevSp = prevSpInSchedule(instance.commandClasses[0x46].data.switchpoints);
					if (prevSp !== null) {
					if (prevSp.state.value == 121) {
						curTemp = $.translate("No frost");
						curTempUnit = '';
					} else if (prevSp.state.value == 122) {
						curTemp = $.translate("Energy saving");
						curTempUnit = '';
					} else if (prevSp.state.value < 120)
						curTemp += prevSp.state.value * 0.1;
					else if (prevSp.state.value > 128)
						curTemp += (prevSp.state.value - 256) * 0.1;
					else {
						curTemp = '?';
						curTempUnit = '';
					}
				}
			};
			$('<li device="' + deviceId + '" instance="' + instanceId + '"><span id="thermostat_maintained_temperature" class="intl"></span><span>' + (instanceId != 0?(' (#' + instanceId + ')'):'') + '</span>: <span>' + curThermModeName + ' ' + curTemp + ' ' + curTempUnit + '</span></li>').translate().appendTo(menu);
		}
	});
};

/*
	Network management tab
*/
function showNetworkManagement() {
	$('#nm_last_included_device').bindPath('controller.data.lastIncludedDevice', function(obj, path) {
		$(this).html(obj.value != null ? ($.translate('nm_last_included_device') + ' ' + obj.value + ' (' + getTime(obj.updateTime) + ')') : '');
	});
	$('#nm_last_excluded_device').bindPath('controller.data.lastExcludedDevice', function(obj, path) {
		$(this).html(obj.value != null ? ($.translate('nm_last_excluded_device') + ' ' + (obj.value != 0 ? obj.value : $.translate('nm_last_excluded_device_from_foreign_network')) + ' (' + getTime(obj.updateTime) + ')') : '');
	});
	$('#nm_count_jobs_enable').bindPath('controller.data.countJobs', function(obj, path) {
		if (obj.value) {
			$('#nm_count_jobs_enable').hide();
			$('#nm_count_jobs_disable').show();
		} else {
			$('#nm_count_jobs_enable').show();
			$('#nm_count_jobs_disable').hide();
		};
	});
	$('#nm_queue_state').bindPath('controller.data.nonManagmentJobs', function(obj, path) {
		if (ZWaveAPIData.controller.data.countJobs.value)
			$(this).html(obj.value > 0 ? ($.translate('nm_queue_busy') + ': ' + obj.value) : $.translate('nm_queue_ready'));
		else
			$(this).html($.translate('nm_queue_count_jobs_disabled'));
	});
	$('#nm_controller_state').bindPath('controller.data.controllerState', function(obj, path) {
		$(this).html($.translate('nm_controller_state_' + obj.value.toString()));
		if (obj.value == 0)
			$(this).removeClass('red')
		else
			$(this).addClass('red')

		if (in_array(obj.value, [1, 2, 3, 4])) {
			$('#nm_include_start').hide();
			$('#nm_include_stop').show();
		} else {
			$('#nm_include_start').show();
			$('#nm_include_stop').hide();
		};
		if (in_array(obj.value, [5, 6, 7])) {
			$('#nm_exclude_start').hide();
			$('#nm_exclude_stop').show();
		} else {
			$('#nm_exclude_start').show();
			$('#nm_exclude_stop').hide();
		};
		if (in_array(obj.value, [8, 9, 10, 11, 12])) {
			$('#nm_learn_start').hide();
			$('#nm_learn_stop').show();
		} else {
			$('#nm_learn_start').show();
			$('#nm_learn_stop').hide();
		};
		if (in_array(obj.value, [13, 14, 15, 16])) {
			$('#nm_controller_change_start').hide();
			$('#nm_controller_change_stop').show();
		} else {
			$('#nm_controller_change_start').show();
			$('#nm_controller_change_stop').hide();
		};
	});
	$('#nm_suc_current').bindPath('controller.data.SISPresent,controller.data.SUCNodeId', function() {
		$(this).html((ZWaveAPIData.controller.data.SUCNodeId.value != 0)?((ZWaveAPIData.controller.data.SISPresent.value?'SIS':'SUC') + ' ' + $.translate('nm_suc_node_id_is') + ': ' + ZWaveAPIData.controller.data.SUCNodeId.value.toString()):$.translate('nm_suc_not_present'));
	});
	$('button.canAdd').bindPath('controller.data.isPrimary', function (obj) {
		$(this).button(obj.value?'enable':'disable');
	});
	$('button.isRealPrimary').bindPath('controller.data.isRealPrimary', function(obj) {
		$(this).button(obj.value?'enable':'disable');
	});
	$('button.hasSUCnotSelf').bindPath('controller.data.SUCNodeId', function (obj) {
		$(this).button((obj.value && obj.value != ZWaveAPIData.controller.data.nodeId.value)?'enable':'disable');
	});
	$('#nm_remove_failed_node').bindPath('devices,devices[*].data.isFailed', function() {
		$(this).empty();
		if (ZWaveAPIData.controller.data.isPrimary.value) {
			var select = this;
			$.each(ZWaveAPIData.devices, function (nodeId, dev) {
				if (dev.data.isFailed.value)
					$(select).append($('<option></option>').val(nodeId).html(nodeId));
			});
		};
		$('#nm_remove_failed').button($(this).find('option').size()?'enable':'disable');
	});
	$('#nm_mark_battery_as_failed_node').bindPath('devices,devices[*].data.isListening,devices[*].data.isFailed', function() {
		$(this).empty();
		if (ZWaveAPIData.controller.data.isPrimary.value) {
			var select = this;
			$.each(ZWaveAPIData.devices, function (nodeId, dev) {
				if (!dev.data.isListening.value && !dev.data.isFailed.value)
					$(select).append($('<option></option>').val(nodeId).html(nodeId));
			});
		};
		$('#nm_mark_battery_as_failed').button($(this).find('option').size()?'enable':'disable');
	});
	$('#nm_suc_node').bindPath('devices', function() {
		$(this).empty();
		var select = this;
		$.each(ZWaveAPIData.devices, function (nodeId, dev) {
			if (dev.data.basicType.value == 2)
				$(select).append($('<option></option>').val(nodeId).html(nodeId));
		});
	});
	$('.learnMode').bindPath('devices', function () {
		$(this).button((!ZWaveAPIData.controller.data.isRealPrimary.value || $.objLen(ZWaveAPIData.devices) <= 2 /* self and 255 */) ? 'enable':'disable');
	});
	$('#nm_role_in_network').bindPath('devices,controller.data.isPrimary', function () {                
		$(this).html($.translate(
			($.objLen(ZWaveAPIData.devices) <= 2) ?
				'nm_learn_mode_empty_network' :
				(ZWaveAPIData.controller.data.isRealPrimary.value ?
					(ZWaveAPIData.controller.data.SUCNodeId.value ?
						'nm_learn_mode_you_are_primary_and_sis' :
						'nm_learn_mode_you_are_primary_no_sis'
					) :
					(ZWaveAPIData.controller.data.isPrimary.value ?
						'nm_learn_mode_you_are_secondary_can_add' :
						'nm_learn_mode_you_are_secondary_can_not_add'
					)
				)
		));
	});
	$('#nm_can_add_in_network').bindPath('controller.data.isPrimary', function (obj) {
		$(this).html($.translate(obj.value ? 'nm_can_add' : 'nm_can_not_add'));
	});

	$('#nm_bridge').hide();
};

function hideNetworkManagement() {
	$(':data(jQuery.triggerPath)', '#networkmanagement').unbindPath();
};

/*
	Controller Info tab
*/
function showControllerInfo() {
	$('#controller_info').bindPath('controller.data.*', updateControllerInfo);
};

function hideControllerInfo() {
	$('#controller_info').unbindPath();
};

function updateControllerInfo(obj, path) {
	if (path == 'controller.data.nonManagmentJobs')
		return; // we don't want to redraw this page on each (de)queued packet

	var homeId = ZWaveAPIData.controller.data.homeId.value;
	var nodeId = ZWaveAPIData.controller.data.nodeId.value;

	var canAdd = ZWaveAPIData.controller.data.isPrimary.value;
	var isRealPrimary = ZWaveAPIData.controller.data.isRealPrimary.value;
	var haveSIS = ZWaveAPIData.controller.data.SISPresent.value;
	//var isSUC = ZWaveAPIData.controller.data.isSUC.value;
	var SUCNodeID = ZWaveAPIData.controller.data.SUCNodeId.value;

	var vendor = ZWaveAPIData.controller.data.vendor.value;
	var ZWChip = ZWaveAPIData.controller.data.ZWaveChip.value;
	var productId = ZWaveAPIData.controller.data.manufacturerProductId.value;
	var productType = ZWaveAPIData.controller.data.manufacturerProductType.value;

	var sdk = ZWaveAPIData.controller.data.SDK.value;
	var libType = ZWaveAPIData.controller.data.libType.value;
	var api = ZWaveAPIData.controller.data.APIVersion.value;
	
	var revId = ZWaveAPIData.controller.data.softwareRevisionId.value;
	var revVer = ZWaveAPIData.controller.data.softwareRevisionVersion.value;
	var revDate = ZWaveAPIData.controller.data.softwareRevisionDate.value;

	$('#ctrl_info_nodeid_value').html(nodeId);
	$('#ctrl_info_homeid_value').html('0x' + ('00000000' + (homeId + (homeId < 0 ? 0x100000000 : 0)).toString(16)).slice(-8));
	$('#ctrl_info_primary_value').html(canAdd?'yes':'no');
	$('#ctrl_info_real_primary_value').html(isRealPrimary?'yes':'no');
	$('#ctrl_info_suc_sis_value').html((SUCNodeID != 0)?(SUCNodeID.toString() + ' (' + (haveSIS?'SIS':'SUC') + ')'):$.translate('nm_suc_not_present'));

	$('#ctrl_info_hw_vendor_value').html(vendor);
	$('#ctrl_info_hw_product_value').html(productType.toString() + " / " + productId.toString());
	$('#ctrl_info_hw_chip_value').html(ZWChip);

	$('#ctrl_info_sw_lib_value').html(libType);
	$('#ctrl_info_sw_sdk_value').html(sdk);
	$('#ctrl_info_sw_api_value').html(api);

	$('#ctrl_info_sw_rev_ver_value').html(revVer);
	$('#ctrl_info_sw_rev_id_value').html(revId);
	$('#ctrl_info_sw_rev_date_value').html(revDate);
	
	var funcList = '';
	var _fc = array_unique(ZWaveAPIData.controller.data.capabilities.value.concat(ZWaveAPIData.controller.data.functionClasses.value));
	_fc.sort(function(a,b) { return a - b });
	$.each(_fc, function (index, func) {
		var fcIndex = ZWaveAPIData.controller.data.functionClasses.value.indexOf(func);
		var capIndex = ZWaveAPIData.controller.data.capabilities.value.indexOf(func);
		var fcName = (fcIndex != -1) ? ZWaveAPIData.controller.data.functionClassesNames.value[fcIndex] : 'Not implemented';
		funcList += '<span style="color: ' + ((capIndex != -1) ? ((fcIndex != -1) ? '' : 'gray') : 'red') + '">' + fcName + ' (0x' + ('00' + func.toString(16)).slice(-2) + ')</span>, ';
	});
	$('#ctrl_info_capabilities').html(funcList);
};


/*
	Routing table tab
*/
function updateRoutingTable() {
	var skipPortableAndVirtual = true; // to minimize routing table by removing not interesting lines
	var routingTable = '';
	var routingTableHeader = '';
	$.each(ZWaveAPIData.devices, function (nodeId, node) {
		if (nodeId == 255) return;
		if (skipPortableAndVirtual && (node.data.isVirtual.value || node.data.basicType.value == 1)) return;

		var routesCount = getRoutesCount(nodeId);
		routingTableHeader += '<td class="rtHeader">' + nodeId + '</td>';
		routingTable += '<tr><td class="rtNodesNames">' + device_name(nodeId) + '</td><td class="rtNodesNames">' + device_area_name(nodeId, 'not_important') + '</td><td class="rtNodes">' + nodeId + '</td>';
		$.each(ZWaveAPIData.devices, function (nnodeId, nnode) {
			if (nnodeId == 255) return;
			if (skipPortableAndVirtual && (nnode.data.isVirtual.value || nnode.data.basicType.value == 1)) return;
			
			var rtClass;
			if (!routesCount[nnodeId])
				routesCount[nnodeId] = new Array(); // create empty array to let next line work
			var routeHops = (routesCount[nnodeId][1] || '0') + '/' + (routesCount[nnodeId][2] || '0');
			if (nodeId == nnodeId || node.data.isVirtual.value || nnode.data.isVirtual.value || node.data.basicType.value == 1 || nnode.data.basicType.value == 1) {
				rtClass = 'rtUnavailable';
				routeHops = '';
			} else if ($.inArray(parseInt(nnodeId, 10), node.data.neighbours.value) != -1)
				rtClass = 'rtDirect';
			else if (routesCount[nnodeId] && routesCount[nnodeId][1] > 1)
				rtClass = 'rtRouted';
			else if (routesCount[nnodeId] && routesCount[nnodeId][1] == 1)
				rtClass = 'rtBadlyRouted';
			else
				rtClass = 'rtNotLinked';
			routingTable += '<td class="rtCell ' + rtClass + '"><span class="geek routeHops">' + routeHops + '</span></td>';
		});
		routingTable += '<td class="rtInfo">' + getUpdated(node.data.neighbours) + '</td></tr>';
	});
	$('#routing_table_div').html('<table id="RoutingTable"><tr><td class="rtHeaderNames">' + $.translate('device_name') + '</td><td class="rtHeaderNames">' + $.translate('area_name') + '</td><td class="rtHeader">' + $.translate('rt_header_node_id') + '</td>' + routingTableHeader + '<td class="rtHeader">' + $.translate('rt_header_update_time') + '</td></tr>' + routingTable + '</table>');
};

function getRoutesCount(nodeId) {
	var routesCount = {};
	$.each(getFarNeighbours(nodeId), function(index, nnode) {
		if (nnode.nodeId in routesCount) {
			if (nnode.hops in routesCount[nnode.nodeId])
				routesCount[nnode.nodeId][nnode.hops]++;
			else
				routesCount[nnode.nodeId][nnode.hops] = 1;
		} else {
			routesCount[nnode.nodeId] = new Array();
			routesCount[nnode.nodeId][nnode.hops] = 1;
		}
	});
	return routesCount;
};

// returns a list of {nodeId, hops}. Can be used to calculate number of routes and minimal hops to a node
function getFarNeighbours(nodeId, exludeNodeIds, hops) {
	if (hops === undefined) {
		var hops = 0;
		var exludeNodeIds = [nodeId];
	};

	if (hops > 2) // Z-Wave allows only 4 routers, but we are interested in only 2, since network becomes unstable if more that 2 routers are used in communications
		return [];

	var nodesList = [];
	$.each(ZWaveAPIData.devices[nodeId].data.neighbours.value, function(index, nnodeId) {
		if (!(nnodeId in ZWaveAPIData.devices))
			return; // skip deviced reported in routing table but absent in reality. This may happen after restore of routing table.
		if (!in_array(nnodeId, exludeNodeIds)) {
			nodesList.push({ nodeId: nnodeId, hops: hops });
			if (ZWaveAPIData.devices[nnodeId].data.isListening.value && ZWaveAPIData.devices[nnodeId].data.isRouting.value)
				$.merge(nodesList, getFarNeighbours(nnodeId, $.merge([nnodeId], exludeNodeIds) /* this will not alter exludeNodeIds */, hops + 1));
		}
	});
	return nodesList;
};

function showRoutingTable() {
	$('#routing_table_div').bindPath('devices,devices[*],devices[*].data.neighbours,devices[*].data.isVirtual,devices[*].data.basicType,devices[*].data.isListening,devices[*].data.isRouting', updateRoutingTable);
};

function hideRoutingTable() {
	$('#routing_table_div').empty().unbindPath(); // devices,devices[*],devices[*].data.neighbours,devices[*].data.isVirtual,devices[*].data.basicType,devices[*].data.isFailed,devices[*].data.isListening,devices[*].data.isRouting
};

function updateNodesNeighbours() {
	runCmd('controller.RequestNetworkUpdate()');
	$.each(ZWaveAPIData.devices, function (nodeId, node) {
		nodeIsVirtual = node.data.isVirtual;
		nodeBasicType = node.data.basicType;
		if (nodeId == 255 || nodeIsVirtual == null || nodeIsVirtual.value == true || nodeBasicType == null || nodeBasicType.value == 1)
			return;
		runCmd('devices[' + nodeId + '].RequestNodeNeighbourUpdate()');
	});
};
// #endif admin

// call function passed as first argument with rest of arguments.
function catch_error() {
	var args = Array.prototype.slice.call(arguments);  
	var func = args.shift();  
	try {
		return func.apply(null, args);
	} catch(err) {
		return; // return nothing
	}
};

function device_icon(nodeId, withText) {
	ico = $('<div device="' + nodeId + '" class="device_icon' + ((nodeId in ZWaveAPIData.devices) ? '' : ' unregistered') + '"><img class="device_icon_img"/>' + (withText?'<textnode class="device_icon_name">'+device_name(nodeId, { withoutId: true })+'</textnode>':'') + '</div>');
	ico.find('.device_icon_img').bind('error', function() {
		if ($(this).attr('src') != 'pics/icons/device_icon_unknown.png')
			$(this).attr('src', 'pics/icons/device_icon_unknown.png');
	}).bindPath('devices[' + nodeId + '].instances[0].commandClasses[' + 0x25 + '].data.level,devices[' + nodeId + '].instances[0].commandClasses[' + 0x26 + '].data.level,deviceIconsUpdateManually', function(obj, path, icon_nodeId) {
		var icon_name_suffix = 'unregistered';
		var extension = 'png'; // default - can be changed to gif to for animated icons
		
		var custom_icon, custom_type;
		if (icon_nodeId == 255)
			icon_name_suffix = 'broadcast';
		else if ((custom_type = $(config.devices).find('[device=' + icon_nodeId + ']').attr('devicetype')) != '' && typeof(device_type_icon) == 'function' && (custom_icon = catch_error(device_type_icon, icon_nodeId, custom_type))) {
			icon_name_suffix = custom_icon; // note, extensin should be included
			extension = '';
		} else if (icon_nodeId in ZWaveAPIData.devices) {
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

				/* SensorBinary is represented by one icon for all states
				case 0x20:
					if (0x30 in ZWaveAPIData.devices[icon_nodeId].instances[0].commandClasses && ZWaveAPIData.devices[icon_nodeId].instances[0].commandClasses[0x30].data.level.value)
						icon_name_suffix += '_255';
					else
						icon_name_suffix += '_0';
					break;
				*/

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
		$(this).attr('src', 'pics/icons/device_icon_' + icon_name_suffix + ((extension != '') ? ('.' + extension) : ''));
	}, nodeId);
	return ico;
};

function deviceIconNames_update(settings) {
	$('.device_icon').each(function() {
		$(this).find('.device_icon_name').html(device_name($(this).attr('device'), { withoutId: true }));
	});
};

function deviceIcons_update(settings) {
	$.triggerPath.update('deviceIconsUpdateManually');
};

// returns DOM list of nodes
function generateDeviceIconList(settings) {
	if (!settings)
		var settings = {};
	$.extend({
		includeController: false,
		includeVirtuals: false,
		includingBroadcast: false,
		addUnregistered: false
	}, settings);
	var dev_cont = $('<div></div>');
	$.each(ZWaveAPIData.devices, function(nodeId, node) {
		if (nodeId == 255 && !settings.includingBroadcast || nodeId == ZWaveAPIData.controller.data.nodeId.value && !settings.includeController || node.data.isVirtual.value && !settings.includeVirtuals)
			return; // skip broadcast, controller and virtual nodes if needed
		dev_cont.append(device_icon(nodeId, true));
	});
	if (settings.addUnregistered)
		dev_cont.append($('<div device="' + 0 + '" class="device_icon unregistered geek"><img src="' + ('pics/icons/device_icon_unregistered.png') +  '" class="device_icon_img"/><span id="config_ui_unregistered_devices" class="intl"></span></div>').translate());

	return dev_cont.children();
};

function delete_device_config(nodeId) {
	$(config_conf).find("devices deviceconfiguration[id=" + nodeId + "]").remove();
};

function importConfigFromFile(targetNodeId) {
	if (!(window.File && window.FileReader)) {
		alert_dialog($.translate('config_ui_import_configuration_browser_incapable'));
		return;
	};

	$('#custom_dialog').html($.translate('config_ui_import_configuration_description') + '<br /><br /><input type="file" id="imported_xml">')
		.css({'max-height': $(document.body).height()-128})
		.dialog({
			modal: true,
			title: $.translate('config_ui_import_configuration_title'),
			width: 'auto',
			open: function() {
				dialog_init(this);
			},
			buttons: {
				cancel : function() {
					$(this).dialog('close');
				},

				config_ui_import_configuration_import : function () {
					var this_dialog = $(this);

					var files = $('#imported_xml').get(0).files;
					if (!files) {
						this_dialog.dialog('close');
						alert_dialog($.translate('config_ui_import_configuration_browser_incapable'));
						return;
					};
					if (!files[0])
						return; // nothing is selected! don't close the dialog.

					var reader = new FileReader();
					reader.onload = function(imported_xml) {
						delete_device_config(targetNodeId);
						$(imported_xml.target.result).find('deviceconfiguration').each(function() {
							$(config_conf).find('devices').append($(this).clone().attr('id', targetNodeId));
						});
						$(imported_xml.target.result).find('runscript').each(function() {
							eval($(this).text());
						});
						this_dialog.dialog('close');
						showConfigUISimpleDevice(targetNodeId);
						$('#imported_xml').get(0).node = document.createElement('deviceconfiguration'); // a hack to let save_enable guess the tab we are in
						save_enable($('#imported_xml').get(0));
					};

					reader.onerror = function() {
						this_dialog.dialog('close');
						alert_dialog($.translate('config_ui_import_configuration_read_error'));
					};

					reader.readAsText(files[0]);
				}
			}
		});
};

function showConfigToSave(sourceNodeId) {
	var div = $('<div></div>');
	$(config_conf).find('devices deviceconfiguration[id=' + sourceNodeId + ']').each(function() {
		div.append($(this).clone().attr('id', '-'));
	});

	$('#custom_dialog').html('<textarea cols="50" rows="10"><devices>\n' + div.html() + '\n<runscript></runscript>\n</devices></textarea>')
		.css({'max-height': $(document.body).height()-128})
		.dialog({
			modal: true,
			title: $.translate('config_ui_configure_save_configuration'),
			width: 'auto',
			open: function() {
				dialog_init(this);
			},
			buttons: {
				ok : function() {
					$(this).dialog('close');
				}
			}
		});
};

function copyConfigFromOtherDevice(targetNodeId) {
	var devices_list = "";
	$.each(ZWaveAPIData.devices, function(nodeId, node) {
		if (nodeId == targetNodeId || nodeId == 255) return;
		devices_list += '<option value="' + nodeId + '">' + ((!node.data.ZDDXMLFile.value || node.data.ZDDXMLFile.value != ZWaveAPIData.devices[targetNodeId].data.ZDDXMLFile.value)? '' : '* ') + device_name(nodeId) + '</option>';
	});
	$('#custom_dialog').html($.translate('config_ui_configure_like_other_description') + '<br /><br /><select id="sourceCopyNodeId_select">' + devices_list + '</select>')
		.css({'max-height': $(document.body).height()-128})
		.dialog({
			modal: true,
			title: $.translate('config_ui_configure_like_other_title'),
			width: 'auto',
			open: function() {
				dialog_init(this);
			},
			buttons: {
				cancel : function() {
					$(this).dialog('close');
				},

				select : function () {
					var sourceCopyNodeId = $('#sourceCopyNodeId_select').val();
					if (typeof(sourceCopyNodeId) == "undefined")
						return;
					delete_device_config(targetNodeId);
					$(config_conf).find('devices deviceconfiguration[id=' + sourceCopyNodeId + ']').each(function() {
						$(config_conf).find('devices').append($(this).clone().attr('id', targetNodeId));
					});
					$(this).dialog('close');
					showConfigUISimpleDevice(targetNodeId);
					$('#sourceCopyNodeId_select').get(0).node = document.createElement('deviceconfiguration'); // a hack to let save_enable guess the tab we are in
					save_enable($('#sourceCopyNodeId_select').get(0));
				}
			}
		});
};

// returns
// - next wakeup time for a device
// - 0 if it is a remote without wakeup
// - null if the device is listening
function nodeNextWakeup(nodeId) {
	if (!(nodeId in ZWaveAPIData.devices))
		return null;

	var node = ZWaveAPIData.devices[nodeId];
	var isListening = node.data.isListening.value;
	var hasWakeup = 0x84 in node.instances[0].commandClasses;
	var isFLiRS = node.data.sensor250.value || node.data.sensor1000.value;

	if (isListening)
		return null; // mains powered device
	else if (!isListening && hasWakeup) {
		var sleepingSince = parseInt(node.instances[0].commandClasses[0x84].data.lastSleep.value, 10);
		var lastWakeup = parseInt(node.instances[0].commandClasses[0x84].data.lastWakeup.value, 10);
		if (isNaN(sleepingSince) || sleepingSince < lastWakeup)
			sleepingSince = lastWakeup;
		var interval = parseInt(node.instances[0].commandClasses[0x84].data.interval.value, 10);
		if (interval == 0)
			interval = NaN; // to indicate that interval and hence next wakeup are unknown
		var nextWakeup = sleepingSince + interval;
		if (isNaN(nextWakeup))
			return 0;
		return nextWakeup;
	} else if (!isListening && isFLiRS)
		return null;
	else
		return 0;
};

function showConfigUISimpleDevice(nodeId) {
	var node = ZWaveAPIData.devices[nodeId];
	var ConfigUIData = $(config_conf);

	var div_cont = $('#deviceconfiguration');

	var assoc_cont = null;
	if (0x85 in node.instances[0].commandClasses) {
		assoc_cont = $('<div class="assoc_cont"><h4 class="cut_link">' + $.translate('associations_list') + '</h4><div class="content"><button id="update_from_device" class="intl" device="' + nodeId + '"></button></div></div>');
		var grp_cont = assoc_cont.find('div.content');
		grp_cont.find('#update_from_device').bind('click', function() {
			for (var grp_get = 1; grp_get <= ZWaveAPIData.devices[$(this).attr('device')].instances[0].commandClasses[0x85].data.groups.value; grp_get++)
				runCmd('devices[' + $(this).attr('device') + '].instances[0].commandClasses[0x85].Get(' + grp_get + ')');
		});
		if (node.instances[0].commandClasses[0x85].data.groups.value >= 1) {
			ConfigUIData.find('devices deviceconfiguration[id=' + nodeId + '][instance=0][commandclass=85][command=Remove]').remove(); // remove these nodes from tree to add them later on save if needed
			var assocGroupsDevices = ConfigUIData.find('devices deviceconfiguration[id=' + nodeId + '][instance=0][commandclass=85][command=Set]');
			grp_cont.get(0).node = document.createElement('deviceconfiguration'); // a hack to let save_enable guess the tab we are in
			for (var grp_num=1; grp_num <= parseInt(node.instances[0].commandClasses[0x85].data.groups.value, 10); grp_num++) {
				var grp_name = ZDDX(nodeId).find('assocgroup[number=' + grp_num + '] description').html() || ($.translate('association_group') + ' ' + grp_num);

				// add group to DOM and attach update handler
				grp_cont.append(
					$('<div device="' + nodeId + '" assocgrp="' + grp_num + '" assocgrpmax="' + 0 + '" class="device_assocgrp"><span class="title">' + grp_name + ' (' + $.translate('assoc_max_nodes') + ' <span id="grp_max"></span>, <span id="last_update"></span>)</span></div>')
						.bindPath('devices[' + nodeId + '].instances[0].commandClasses[' + 0x85 + '].data[' + grp_num + ']', function (obj, path) {
							if (obj != null) {
								var nodeId = $(this).attr('device');
								var groupId = $(this).attr('assocgrp');
								var grp_cont = $(this);

								$(this).attr('assocgrpmax', obj.max.value);
								$(this).find('#grp_max').html(obj.max.value.toString());
								$(this).find('#last_update').html($.translate('config_ui_assoc_group_updated_on') + ' ' + getTime(parseInt(obj.updateTime, 10)));
								if (obj.updateTime > obj.invalidateTime)
									$(this).find('#last_update').removeClass('red');
								else
									$(this).find('#last_update').addClass('red');

								// check all devices currently present in the box and remove/mark them approprietary
								grp_cont.find('.device_icon').each(function () {
									addRemoveDeviceIconToAssocGroupBox(nodeId, groupId, $(this).attr('device'), grp_cont);
								});
								
								// add all devices from ZWave data
								if (obj.nodes.value)
									$.each(obj.nodes.value, function (i, assocNodeId) {
										addRemoveDeviceIconToAssocGroupBox(nodeId, groupId, assocNodeId, grp_cont);
									});

								// notifications about deviation of config from zw $(this).attr('notify', $(this).find('.inZWaveNotInConfig, .inConfigNotInZWave').size()?'yes':'');
							} else {
								$(this).find('#last_update').html($.translate('config_ui_assoc_group_not_interviewed'));
								$(this).find('#grp_max').html('0');
								// notifications about deviation of config from zw $(this).attr('notify', 'yes');
							};
							/*
							// notifications about deviation of config from zw
							var notify = false;
							$(this).parent().each(function() {
								notify = notify || ($(this).attr('notify') !== "");
							});
							$(this).parent().attr('notify', notify?'yes':'');
							markNotification(nodeId);
							*/
						})	
				);

				// get the list of associated devices from the XML
				var re = new RegExp('\\[' + grp_num + ',([0-9]+)\\]');
				
				// put firstly devices from the config into the box. Then bindPath will do the job.
				assocGroupsDevices.filter(function() {
					return re.test($(this).attr("parameter"))
				}).each(function (i, assocDev) {
					m = re.exec($(assocDev).attr("parameter"));
					addRemoveDeviceIconToAssocGroupBox(nodeId, grp_num, parseInt(m[1], 10), grp_cont.find('.device_assocgrp'));
				});
			}
		} else {
			grp_cont.append($('<span>' + $.translate('no_association_groups_found') + '</span>'));
		}
	};

	var config_cont = null;
	if (0x70 in node.instances[0].commandClasses) {
		config_cont = $('<div class="conf_cont"><h4 class="cut_link">' + $.translate('configurations_list') + '</h4><div class="content"><button id="update_from_device" class="intl" device="' + nodeId + '"></button></div></div>');
		var conf_cont = config_cont.find('div.content');
		var have_conf_params = false;
		var conf_update_array = [];
		var config_from_file = ConfigUIData.find('devices deviceconfiguration[id=' + nodeId + '][instance=0][commandclass=70][command=Set]');
		ZDDX(nodeId).find('configparams configparam').each(function (i, conf_html) {
			have_conf_params = true;
			var conf = $(conf_html);
			var conf_num = conf.attr('number');
			var conf_name = conf.find('name').html() || ($.translate('configuration_parameter') + ' ' + conf_num);
			var conf_description = conf.find('description').html() || '';
			var conf_size = conf.attr('size');
			var conf_default_value = null;
			var conf_type = conf.attr('type');

			conf_update_array.push(conf_num);

			// get default value from the XML
			var conf_default = null;
			if (conf.attr('default') !== undefined)
				conf_default = parseInt(conf.attr('default'), 16);

			// get value from the Z-Wave data
			var config_zwave_value = null;
			if (node.instances[0].commandClasses[0x70].data[conf_num] != null && node.instances[0].commandClasses[0x70].data[conf_num].val.value !== "")
				config_zwave_value = node.instances[0].commandClasses[0x70].data[conf_num].val.value;

			// get value from the XML								
			var re = new RegExp('\\[' + conf_num + ',([0-9]*),([0-9]+)\\]'); // * in the second param is to allow [1,,3] when there is not default value.

			var config_param_from_file = config_from_file.filter(function() {
				return re.test($(this).attr("parameter"))
			});

			var config_config_value;
			var config_conf_el;
			if (config_param_from_file.size() > 0) {
				var m = re.exec($(config_param_from_file.get(0)).attr("parameter")); // we get only the first element. there should be only one anyway
				config_config_value = parseInt(m[1], 10);
				config_conf_el = config_param_from_file.get(0);
			} else {
				if (config_zwave_value !== null)
					config_config_value = config_zwave_value;
				else
					config_config_value = conf_default;

				config_conf_el = document.createElement('deviceconfiguration');
				config_conf_el.setAttribute('id', nodeId.toString());
				config_conf_el.setAttribute('instance', '0');
				config_conf_el.setAttribute('commandclass', '70');
				config_conf_el.setAttribute('command', 'Set');
				config_conf_el.setAttribute('parameter', '[' + [conf_num, config_config_value, conf_size].toString() + ']');
				ConfigUIData.find('devices').append(config_conf_el);
			}
			var param_cont = $('<div class="configuration_parameter" conf_num="' + conf_num + '" conf_size="' + conf_size + '"><span>' + conf_num + '. ' + conf_name + ': </span><span class="value config" value="[]"></span><div class="configuration_updated"><span class="intl" id="updated"></span>: <span id="update_time"></span></div><div class="configuration_default"></div><div class="configuration_description">' + conf_description + '</div></div>');
			param_cont.get(0).node = config_conf_el;

			// value selector by type
			var conf_method_descr;
			switch (conf_type) {
				case 'rangemapped':
					var param_struct_arr = [];
					var conf_param_options = '';
					conf.find('value').each(function (i, value_html) {
						var value = $(value_html);
						var value_from = parseInt(value.attr("from"), 16);
						var value_to = parseInt(value.attr("to"), 16);
						var value_description = value.find("description").html();
						var value_repr = value_from; // representative value for the range
						if (conf_default !== null)
							if (value_from <= conf_default && conf_default <= value_to) {
								conf_default_value = value_description;
								value_repr = conf_default;
							}
						param_struct_arr.push({
							label: value_description,
							type: {
								fix: {
									value: value_repr
								}
							}
						});
					});
					conf_method_descr = {
						label: '',
						type: {
							enumof: param_struct_arr
						}
					};
					break;

				case 'range':
					var param_struct_arr = [];
					conf.find('value').each(function (i, value_html) {
						var value = $(value_html);
						var value_from = parseInt(value.attr("from"), 16);
						var value_to = parseInt(value.attr("to"), 16);
						var value_description = value.find("description").html();
						if (conf_default !== null)
							conf_default_value = conf_default;
						if (value_from != value_to)
							param_struct_arr.push({
								label: value_description,
								type: {
									range: {
										min: value_from,
										max: value_to
									}
								}
							});
						else // this is a fix value
							param_struct_arr.push({
								label: value_description,
								type: {
									fix: {
										value: value_from
									}
								}
							});
					});
					if (param_struct_arr.length > 1)
						conf_method_descr = {
							label: '',
							type: {
								enumof: param_struct_arr
							}
						};
					else if (param_struct_arr.length == 1)
						conf_method_descr = param_struct_arr[0];
					break;

				case 'bitset':
					var param_struct_arr = [];
					var conf_param_options = '';
					var conf_default_value_arr = new Object;
					if (conf_default !== null) {
						var bit = 0;
						do {
							if ((1 << bit) & conf_default)
								conf_default_value_arr[bit] = 'Bit ' + bit + ' set';
						} while ((1 << (bit++)) < conf_default);
					};
					conf.find('value').each(function (i, value_html) {
						var value = $(value_html);
						var value_from = parseInt(value.attr("from"), 16);
						var value_to = parseInt(value.attr("to"), 16);
						var value_description = value.find("description").html();
						if (conf_default !== null) {
							if (value_from == value_to) {
								if ((1 << value_from) & conf_default)
									conf_default_value_arr[value_from] = value_description;
							} else {
								conf_default_value_arr[value_from] = (conf_default >> value_from) & ((1 << (value_to - value_from + 1)) - 1)
								for (var bit = value_from+1; bit <= value_to; bit++)
									delete conf_default_value_arr[bit];
							}
						};
						if (value_from == value_to)
							param_struct_arr.push({
								label: value_description,
								type: {
									bitcheck: {
										bit: value_from
									}
								}
							});
						else
							param_struct_arr.push({
								label: value_description,
								type: {
									bitrange: {
										bit_from: value_from,
										bit_to: value_to
									}
								}
							});
					});
					if (conf_default !== null) {
						conf_default_value = '';
						for (var ii in conf_default_value_arr)
							conf_default_value += conf_default_value_arr[ii] + ', ';
						if (conf_default_value.length)
							conf_default_value = conf_default_value.substr(0, conf_default_value.length - 2);
					}
					conf_method_descr = {
						label: '',
						type: {
							bitset: param_struct_arr
						}
					};
					break;

				default:
					conf_cont.append('<span>' + $.translate('unhandled_type_parameter') + ': ' + conf_type + '</span>');
			};
			
			// default value
			param_cont.find('.configuration_default').html((conf_default_value == null) ? $.translate('param_default_value_undefined') : ($.translate('default_value_is') + ': ' + conf_default_value));

			// updatetime block
			param_cont.find('.configuration_updated').bindPath('devices[' + nodeId + '].instances[0].commandClasses[' + 0x70 + '].data[' + conf_num + ']', function(obj, path) {
				if (obj) {
					$(this).find('#update_time').html(getTime(parseInt(obj.updateTime, 10)));
					if (obj.updateTime > obj.invalidateTime)
						$(this).find('#update_time').removeClass('red');
					else
						$(this).find('#update_time').addClass('red');
				}
			});

			// generate gui and init init default values
			if (conf_method_descr) {
				param_cont.find('.value.config')
					.append($('<span></span>').addClass('subtagname'))
					.append($('<span></span>').attr('device', nodeId).attr('value', '[' + [conf_num, config_config_value, conf_size].toString() + ']'));

				var gui_container = param_cont.find('.value.config span[device][value]');
				switch (conf_type) {
					case 'rangemapped':
					case 'range':
						var _gui = method_rawGui([conf_method_descr]);
						var gui=$('<span class="method">'+_gui.html+'</span>');
						gui.find('.parameter').attr('parameter', 1); // a little hack: we make the gui only for the second parameter, so set parameter value to 2-1 = 1
						gui_container.append(gui);
						method_guiInit(gui, [config_config_value]);

						method_guiPrepareLabels(_gui, gui, true, false);
						break;

					case 'bitset':
						$.each(conf_method_descr.type.bitset, function (k, v) {
							var _gui = method_rawGui([v]);
							var gui=$('<span class="method">'+_gui.html+'</span>');
							gui.find('.parameter').attr('parameter', 1); // a little hack: we make the gui only for the second parameter, so set parameter value to 2-1 = 1
							gui_container.append(gui);
							method_guiInit(gui, [config_config_value]);

							method_guiPrepareLabels(_gui, gui, true, false, true); // append lables
						});
						break;
				};

				gui_container.bindPath('devices[' + nodeId + '].instances[0].commandClasses[' + 0x70 + '].data[' + conf_num + ']', function (obj, path) {
					if (obj)
						method_guiShow($(this), [null, parseInt(obj.val.value, 10), null]); // conf_num and size are null, since no interface would be interested in them anyway
				});
			};

					/*
					// notifications about deviation of config from zw
					$(this).parents('.configuration_parameter').attr('notify', ConfigUIData.find('devices deviceconfiguration[id=' + nodeId + '][instance=0][commandclass=70][command=Set][parameter="[' + obj.name + ',' + obj.val.value + ',' + obj.size.value + ']"]').size() ? '':'yes');
					var notify = false;
					$(this).parents('.conf_cont').each(function() {
						notify = notify || ($(this).attr('notify') !== '');
					});
					$(this).parents('.conf_cont').attr('notify', notify?'yes':'');
					markNotification(nodeId);
					*/

			conf_cont.append(param_cont);
			
		});
		if (!have_conf_params) {
			conf_cont.append($('<span>' + $.translate('config_ui_missing_configuration_parameters_in_xml') + '</span>'));
			// notifications about deviation of config from zw .attr('notify', 'yes'); markNotification(nodeId);
			conf_cont.find('#update_from_device').remove(); // remove the button if we don't know config parameters
		} else {
			conf_cont.find('#update_from_device').bind('click', function() {
				for (var param_indx in conf_update_array)
					runCmd('devices[' + $(this).attr('device') + '].instances[0].commandClasses[0x70].Get(' + conf_update_array[param_indx] + ')');
			});
		}
	};

	var switchall_cont = null;
	if (0x27 in node.instances[0].commandClasses) {
		switchall_cont = $('<div class="switchall_cont"><h4 class="cut_link">' + $.translate('switchall_list') + '</h4><div><button id="update_from_device" class="intl" device="' + nodeId + '"></button><div class="content"></div></div></div>').addClass('value switchall');
		switchall_cont.find('#update_from_device').bind('click', function() {
			runCmd('devices[' + $(this).attr('device') + '].instances[0].commandClasses[0x27].Get()');
		});
		var switchall_config = ConfigUIData.find('devices deviceconfiguration[id=' + nodeId + '][instance=0][commandclass=27][command=Set]');
		var switchall_conf_value;
		var switchall_conf_el;
		if (switchall_config.size() > 0) {
			var re = new RegExp('\\[([0-9]+)\\]');
			var rem = re.exec(switchall_config.attr("parameter"));
			switchall_conf_value = (rem)?parseInt(rem[1], 10):null;
			switchall_conf_el = switchall_config.get(0);
		} else {
			switchall_conf_value = 1; // by default switch all off group only
			switchall_conf_el = document.createElement('deviceconfiguration');
			switchall_conf_el.setAttribute('id', nodeId.toString());
			switchall_conf_el.setAttribute('instance', '0');
			switchall_conf_el.setAttribute('commandclass', '27');
			switchall_conf_el.setAttribute('command', 'Set');
			switchall_conf_el.setAttribute('parameter', '[' + switchall_conf_value.toString() + ']');
			ConfigUIData.find('devices').append(switchall_conf_el);
		}
		switchall_cont.get(0).node = switchall_conf_el;
		var _gui = method_rawGui(getMethodSpec(nodeId, 0, 0x27, 'Set'));
		var gui=$('<span class="method">'+_gui.html+'</span>');
		switchall_cont.find('div.content')
			.append($('<span></span>').addClass('subtagname'))
			.append($('<span></span>').attr('device', nodeId).attr('value', '[' + switchall_conf_value.toString() + ']')
					.append(gui))
			.append($('<div class="switchall_updated"><span class="intl" id="updated"></span>: <span id="update_time"></span></div>'));
		method_guiInit(gui, [switchall_conf_value]);
		method_guiPrepareLabels(_gui, gui, true, false);

		gui.bindPath('devices[' + nodeId + '].instances[0].commandClasses[' + 0x27 + '].data.mode', function (obj, path) {
			if (obj) {
				method_guiShow($(this), [parseInt(obj.value, 10)]);
				$(this).parent().parent().find('#update_time').html(getTime(parseInt(obj.updateTime, 10)));
				if (obj.updateTime > obj.invalidateTime)
					$(this).parent().parent().find('#update_time').removeClass('red');
				else
					$(this).parent().parent().find('#update_time').addClass('red');
			}
			/*
			// notifications about deviation of config from zw
			$(this).parents('.switchall_cont').attr('notify', ConfigUIData.find('devices deviceconfiguration[id=' + nodeId + '][instance=0][commandclass=27][command=Set][parameter="[' + obj.value + ']"]').size() ? '':'yes');
			markNotification(nodeId);
			*/
		});
	};

	var protection_cont = null;
	if (0x75 in node.instances[0].commandClasses) {
		protection_cont = $('<div class="protection_cont"><h4 class="cut_link">' + $.translate('protection_list') + '</h4><div><button id="update_from_device" class="intl" device="' + nodeId + '"></button><div class="content"></div></div></div>').addClass('value protection');
		protection_cont.find('#update_from_device').bind('click', function() {
			runCmd('devices[' + $(this).attr('device') + '].instances[0].commandClasses[0x75].Get()');
		});
		var protection_version = node.instances[0].commandClasses[0x75].data.version.value;
		var protection_config = ConfigUIData.find('devices deviceconfiguration[id=' + nodeId + '][instance=0][commandclass=75][command=Set]');
		var protection_conf_value;
		var protection_conf_rf_value;
		var protection_conf_el;
		if (protection_config.size() > 0) {
			var re = new RegExp('\\[([0-9]+)(,([0-9]+))?\\]'); // rem[1] and rem[3] are needed!! 
			var rem = re.exec(protection_config.attr("parameter"));
			protection_conf_value = (rem)?parseInt(rem[1], 10):0;
			protection_conf_rf_value = (rem && rem[3])?parseInt(rem[3], 10):0;
			protection_conf_el = protection_config.get(0);
		} else {
			protection_conf_value = 0; // by default protection is disabled
			protection_conf_rf_value = 0; // by default protection is disabled
			protection_conf_el = document.createElement('deviceconfiguration');
			protection_conf_el.setAttribute('id', nodeId.toString());
			protection_conf_el.setAttribute('instance', '0');
			protection_conf_el.setAttribute('commandclass', '75');
			protection_conf_el.setAttribute('command', 'Set');
			protection_conf_el.setAttribute('parameter', '[' + protection_conf_value.toString() + ',' + protection_conf_rf_value.toString() + ']');
			ConfigUIData.find('devices').append(protection_conf_el);
		}
		protection_cont.get(0).node = protection_conf_el;
		var _gui = method_rawGui(getMethodSpec(nodeId, 0, 0x75, 'Set'));
		var gui=$('<span class="method">'+_gui.html+'</span>');
		protection_cont.find('div.content')
			.append($('<span></span>').addClass('subtagname'))
			.append($('<span></span>').attr('device', nodeId).attr('value', '[' + protection_conf_value.toString() + ',' + protection_conf_rf_value.toString() + ']')
					.append(gui))
			.append($('<div class="protection_updated"><span class="intl" id="updated"></span>: <span id="update_time"></span></div>'));
		method_guiInit(gui, (protection_version == 2) ? [protection_conf_value, protection_conf_rf_value] : [protection_conf_value]);
		method_guiPrepareLabels(_gui, gui, true, false);

		gui.bindPath('devices[' + nodeId + '].instances[0].commandClasses[' + 0x75 + '].data.state', function (obj, path) {
			if (obj) {
				var state = parseInt(ZWaveAPIData.devices[nodeId].instances[0].commandClasses[0x75].data.state.value, 10);
				var rfState = parseInt(ZWaveAPIData.devices[nodeId].instances[0].commandClasses[0x75].data.rfState.value, 10);
				method_guiShow($(this), (protection_version == 2) ? [state, rfState] : [state]);
				$(this).parent().parent().find('#update_time').html(getTime(parseInt(obj.updateTime, 10)));
				if (obj.updateTime > obj.invalidateTime)
					$(this).parent().parent().find('#update_time').removeClass('red');
				else
					$(this).parent().parent().find('#update_time').addClass('red');
			}
			/*
			// notifications about deviation of config from zw
			$(this).parents('.protection_cont').attr('notify', ConfigUIData.find('devices deviceconfiguration[id=' + nodeId + '][instance=0][commandclass=75][command=Set][parameter="[' + obj.value + ']"]').size() ? '':'yes');
			markNotification(nodeId);
			*/
		});
		// we do not check for interview before rendering the UI
		//  protection_cont.append($('<span>' + $.translate('no_protection_interface_do_interview') + '</span>'));
	};

	var wakeup_cont = null;
	if (0x84 in node.instances[0].commandClasses) {
		wakeup_cont = $('<div class="wakeup_cont"><h4 class="cut_link">' + $.translate('wakeup_list') + '</h4><div class="content"><button id="update_from_device" class="intl" device="' + nodeId + '"></button></div></div>').addClass('value wakeup');
		wakeup_cont.find('#update_from_device').bind('click', function() {
			runCmd('devices[' + $(this).attr('device') + '].instances[0].commandClasses[0x84].Get()');
		});
		var wakeup_zwave_min = (node.instances[0].commandClasses[0x84].data.version.value == 1) ? 0 : node.instances[0].commandClasses[0x84].data.min.value;
		var wakeup_zwave_max = (node.instances[0].commandClasses[0x84].data.version.value == 1) ? 0xFFFFFF : node.instances[0].commandClasses[0x84].data.max.value;
		var wakeup_zwave_value = node.instances[0].commandClasses[0x84].data.interval.value;
		var wakeup_zwave_default_value = (node.instances[0].commandClasses[0x84].data.version.value == 1) ? 86400 : node.instances[0].commandClasses[0x84].data['default'].value; // default is a special keyword in JavaScript
		var wakeup_zwave_nodeId = node.instances[0].commandClasses[0x84].data.nodeId.value;
		if (wakeup_zwave_min !== '' && wakeup_zwave_max !== '') {
			var gui_descr = getMethodSpec(nodeId, 0, 0x84, 'Set');
			gui_descr[0].type.range.min = parseInt(wakeup_zwave_min, 10);
			gui_descr[0].type.range.max = parseInt(wakeup_zwave_max, 10);
			var wakeup_config = ConfigUIData.find('devices deviceconfiguration[id=' + nodeId + '][instance=0][commandclass=84][command=Set]');
			var wakeup_conf_el;
			var wakeup_conf_value;
			var wakeup_conf_nodeId;
			if (wakeup_config.size() == 1) {
				var re = new RegExp('\\[([0-9]+),([0-9]+)\\]');
				var rem = re.exec(wakeup_config.attr("parameter"));
				wakeup_conf_value = (rem)?parseInt(rem[1], 10):null;
				wakeup_conf_nodeId = (rem)?parseInt(rem[2], 10):null;
				wakeup_conf_el = wakeup_config.get(0);
			} else {
				if (wakeup_zwave_value != "" && wakeup_zwave_value != 0 && wakeup_zwave_nodeId != "") {
					// not defined in config: adopt devices values
					wakeup_conf_value = parseInt(wakeup_zwave_value, 10);
					wakeup_conf_nodeId = parseInt(wakeup_zwave_nodeId, 10);
				} else {
					// values in device are missing. Use defaults
					wakeup_conf_value = parseInt(wakeup_zwave_default_value, 10);
					wakeup_conf_nodeId = parseInt(ZWaveAPIData.controller.data.nodeId.value, 10);
				};
				wakeup_conf_el = document.createElement('deviceconfiguration');
				wakeup_conf_el.setAttribute('id', nodeId.toString());
				wakeup_conf_el.setAttribute('instance', '0');
				wakeup_conf_el.setAttribute('commandclass', '84');
				wakeup_conf_el.setAttribute('command', 'Set');
				wakeup_conf_el.setAttribute('parameter', '[' + [wakeup_conf_value, wakeup_conf_nodeId].toString() + ']');
				ConfigUIData.find('devices').append(wakeup_conf_el);
			};
			wakeup_cont.get(0).node = wakeup_conf_el;

			var _gui = method_rawGui(gui_descr);
			var gui=$('<span class="method">'+_gui.html+'</span>');
			wakeup_cont.find('div.content')
				.append($('<span></span>').addClass('subtagname'))
				.append($('<span></span>').attr('device', nodeId).attr('value', '[' + [wakeup_conf_value, wakeup_conf_nodeId].toString() + ']')
					.append(gui))
				.append($('<div class="wakeup_updated"><span class="intl" id="updated"></span>: <span id="update_time"></span></div>'));
			method_guiInit(gui, [wakeup_conf_value, wakeup_conf_nodeId]);
			method_guiPrepareLabels(_gui, gui, true, true);

			gui.bindPath('devices[' + nodeId + '].instances[0].commandClasses[' + 0x84 + '].data', function (obj, path, nodeId) {
				if (obj) {
					method_guiShow($(this), [obj.interval.value, obj.nodeId.value]);
					$(this).parent().parent().find('#update_time').html(getTime(parseInt(obj.updateTime, 10)));
					if (obj.updateTime > obj.invalidateTime)
						$(this).parent().parent().find('#update_time').removeClass('red');
					else
						$(this).parent().parent().find('#update_time').addClass('red');
				}
				/*
				// notifications about deviation of config from zw
				$(this).parents('.wakeup_cont').attr('notify', ConfigUIData.find('devices deviceconfiguration[id=' + nodeId + '][instance=0][commandclass=84][command=Set][parameter="[' + obj.interval.value + ',' + obj.nodeId.value + ']"]').size() ? '':'yes');
				markNotification(nodeId);
				*/
			}, nodeId);
		} else
			wakeup_cont.find('div.content').append($('<span>' + $.translate('config_ui_wakeup_no_min_max') + '</span>'));
			// notifications about deviation of config from zw $(this).parents('.wakeup_cont').attr('notify', 'yes'); markNotification(nodeId);
	};

	var deviceDescriptionTag = ZDDX(nodeId).find('deviceDescription');
	var deviceDescriptionAppVersion = parseInt(node.data.applicationMajor.value, 10);
	var deviceDescriptionAppSubVersion = parseInt(node.data.applicationMinor.value, 10);
	if (isNaN(deviceDescriptionAppVersion)) deviceDescriptionAppVersion = '-';
	if (isNaN(deviceDescriptionAppSubVersion)) deviceDescriptionAppSubVersion = '-';

	var deviceImage = ZDDX(nodeId).find('resourceLinks deviceImage').attr('url');
	if (deviceImage)
		deviceImage =  deviceImage;
	else
		deviceImage = 'pics/no_device_image.png';

	var resources = ZDDX(nodeId).find('resourceLinks resourceLink');
	var resources_list = new Array;
	resources.each(function() {
		var resource_name = $(this).find('description').text();
		resources_list.push('<a class="external_link" href="' + $(this).attr('url') + '">' + (resource_name?resource_name:$.translate('device_description_resource_no_name')) + '</a>');
	});
	
	var zwNodeName = '', zwNodeLocation = '';
	if (0x77 in node.instances[0].commandClasses) {
		// NodeNaming
		zwNodeName = node.instances[0].commandClasses[0x77].data.nodename.value;
		zwNodeLocation = node.instances[0].commandClasses[0x77].data.location.value;
		if (zwNodeName != '')
			zwNodeName = ' (' + zwNodeName + ')';
		if (zwNodeLocation != '')
			zwNodeLocation = ' (' + zwNodeLocation + ')';
	}
	var description_cont = $(
			'<div><h4 class="cut_link">' + $.translate('device_description_title') + '</h4>' +
				'<table class="descr_table content">' + 
					'<tr><td>' + $.translate('device_node_id') + ':</td><td>' + nodeId + '</td><td rowspan="12"><img src="' + deviceImage + '" class="device_image" /></td></tr>' +
					'<tr><td>' + $.translate('device_node_name') + ':</td><td>' + device_name(nodeId) + zwNodeName + '</td></tr>' +
					'<tr><td>' + $.translate('Area') + ':</td><td>' + device_area_name(nodeId, 'not_important') + zwNodeLocation + '</td></tr>' +
					'<tr><td>' + $.translate('device_node_type') + ':</td><td>' + device_type(nodeId) + '</td></tr>' +
					'<tr><td>' + $.translate('device_description_brand') + ':</td><td>' + (deviceDescriptionTag.find('brandName').html() || node.data.vendorString.value) + '</td></tr>' + 
					'<tr><td>' + $.translate('device_description_device_type') + ':</td><td>' + node.data.deviceTypeString.value + '</td></tr>' + 
					'<tr><td>' + $.translate('device_description_product') + ':</td><td>' + (deviceDescriptionTag.find('productName').html() || '') + '</td></tr>' + 
					'<tr><td>' + $.translate('device_description_description') + ':</td><td>' + (deviceDescriptionTag.find('description').html() || '') + '</td></tr>' + 
					'<tr><td>' + $.translate('device_description_inclusion_note') + ':</td><td>' + (deviceDescriptionTag.find('inclusionNote').html() || '') + '</td></tr>' + 
					(deviceDescriptionTag.find('wakeupNote').html() ? ('<tr><td>' + $.translate('device_description_wakeup_note') + ':</td><td>' + (deviceDescriptionTag.find('wakeupNote').html() || '') + '</td></tr>'):'') + 
					'<tr><td>' + $.translate('device_description_resources') + ':</td><td>' + resources_list.join(', ') + '</td></tr>' + 
					'<tr><td>' + $.translate('device_description_interview') + ':</td><td><span id="interview_stage_description"></span><br class="geek"/><span class="geek" id="interview_stage"></span></td></tr>' + 
					'<tr><td>' + $.translate('device_sleep_state') + ':</td><td id="device_state"></td></tr>' + 
					'<tr><td>' + $.translate('device_queue_length') + ':</td><td id="queue_length"></td></tr>' + 
					'<tr class="geek"><td>' + $.translate('device_description_app_version') + ':</td><td>' + deviceDescriptionAppVersion + '.' + deviceDescriptionAppSubVersion + '</td></tr>' + 
					'<tr class="geek"><td>' + $.translate('device_description_sdk_version') + ':</td><td>' + node.data.SDK.value + '</td></tr>' + 
				'</table>' + 
			'</div>'
	);

		
	description_cont.find('#interview_stage').bindPath('devices[' + nodeId + '].data.nodeInfoFrame,devices[' + nodeId + '].instances[*].commandClasses[*].data.interviewDone', function (data, path, id) {
		var istages = [];
		istages.push((ZWaveAPIData.devices[id].data.nodeInfoFrame.value && ZWaveAPIData.devices[id].data.nodeInfoFrame.value.length) ? '+' : '-');
		istages.push('&nbsp;');
		istages.push((0x86 in ZWaveAPIData.devices[id].instances[0].commandClasses) ? (ZWaveAPIData.devices[id].instances[0].commandClasses[0x86].data.interviewDone.value ? '+' : (ZWaveAPIData.devices[id].instances[0].commandClasses[0x86].data.interviewCounter.value > 0 ? '.' : '&oslash;')) : '+'); // Version
		istages.push((0x72 in ZWaveAPIData.devices[id].instances[0].commandClasses) ? (ZWaveAPIData.devices[id].instances[0].commandClasses[0x72].data.interviewDone.value ? '+' : (ZWaveAPIData.devices[id].instances[0].commandClasses[0x72].data.interviewCounter.value > 0 ? '.' : '&oslash;')) : '+'); // ManufacturerSpecific
		istages.push((0x60 in ZWaveAPIData.devices[id].instances[0].commandClasses) ? (ZWaveAPIData.devices[id].instances[0].commandClasses[0x60].data.interviewDone.value ? '+' : (ZWaveAPIData.devices[id].instances[0].commandClasses[0x60].data.interviewCounter.value > 0 ? '.' : '&oslash;')) : '+'); // MultiChannel
		var moreCCs = false;
		for (var i in ZWaveAPIData.devices[id].instances) {
			istages.push('&nbsp;');
			var instance = ZWaveAPIData.devices[id].instances[i];
			for (var cc in instance.commandClasses) {
				moreCCs = true;
				if ((cc == 0x60 && i != 0) || ((cc == 0x86 || cc == 0x72 || cc == 0x60) && i == 0))
					continue; // skip MultiChannel announced inside a MultiChannel and previously handled CCs.
				istages.push(instance.commandClasses[cc].data.interviewDone.value ? '+' : (instance.commandClasses[cc].data.interviewCounter.value > 0 ? '.' : '&oslash;'));
			}
		};
		if (!moreCCs)
			istages.push('.');
		$(this).html(istages.join(''));

		var descr;
		if (istages.indexOf('&oslash;') == -1) {
			if (istages.indexOf('.') == -1 && istages.indexOf('-') == -1)
				descr = $.translate('device_interview_stage_done');
			else
				descr = $.translate('device_interview_stage_not_complete');
		} else
			descr = $.translate('device_interview_stage_failed');
		$(this).parent().find('#interview_stage_description').html(descr);
	}, nodeId);

	if (!node.data.isListening.value && !node.data.sensor250.value && !node.data.sensor1000.value)
		description_cont.find('#device_state').bindPath('devices[' + nodeId + '].data.isAwake', function(obj, path) {
			$(this).html(obj.value ? ('<img src="pics/icons/status_awake.png"/> ' + $.translate('device_is_active')):('<img src="pics/icons/status_sleep.png"/>' + $.translate('device_is_sleeping')));
		});
	else
		description_cont.find('#device_state').bindPath('devices[' + nodeId + '].data.isFailed', function(obj, path) {
			$(this).html(obj.value ? ('<img src="pics/icons/status_dead.png"/> ' + $.translate('device_is_dead')):('<img src="pics/icons/status_ok.png"/>' + $.translate('device_is_operating')));
		});

	description_cont.find('#queue_length').bindPath('devices[' + nodeId + '].data.queueLength', function(obj, path) {
		if (ZWaveAPIData.controller.data.countJobs.value)
			$(this).html(obj.value);
		else
			$(this).html($.translate('nm_queue_count_jobs_disabled'));
	});

	var selectxml_cont = $(
			'<div><h4 class="cut_link">' + $.translate('selectxml_list') + '</h4>' +
				'<div class="content">' + 
					'<button id="config_ui_select_xml" class="intl"></button>' +
					'<button id="config_ui_request_node_info" class="intl geek"></button>' +
				'<div>' +
			'</div>').translate();
	if (node.data.ZDDXMLFile.value != '')
		selectxml_cont.find('.content').hide();

	var import_export_save_config_cont = $(
			'<div><h4 class="cut_link">' + $.translate('import_export_save_config_title') + '</h4>' +
				'<div class="content" style="display: none;">' + 
					'<button id="config_ui_configure_like_other" class="intl"></button>' +
					'<button id="config_ui_import_configuration" class="intl"></button>' +
					'<br class="geek"/>' + 
					'<button id="config_ui_delete_device_config" class="intl geek"></button>' +
					'<button id="config_ui_show_configuration_xml_link" class="intl geek"></a>' +
				'</div>' + 
			'</div>').translate();

	var apply_notification_text = '', apply_notification_text_time = '';
	var _nodeNextWakeup = nodeNextWakeup(nodeId)
	if (_nodeNextWakeup === null)
		apply_notification_text = 'conf_apply_mains';
	else if (_nodeNextWakeup === 0)
		apply_notification_text = 'conf_apply_remote';
	else {
		apply_notification_text = 'conf_apply_wakeup';
		apply_notification_text_time = getTime(_nodeNextWakeup);
	};

	var apply_cont = $(
			'<div><h4 class="cut_link">' + $.translate('apply_config_tooltip') + '</h4>' +
				'<div class="content">' +
					'<button id="apply_config" class="intl" title="apply_config_tooltip"></button>' +
					'<br /><span id="' + apply_notification_text +'" class="intl"></span> <span>' + apply_notification_text_time + '<span>' +
				'</div>' +
			'</div>').translate();

	var advanced_xml_cont = $(
			'<div class="geek"><h4 class="cut_link">' + $.translate('advanced_xml_title') + '</h4>' +
				'<div class="content" style="display: none;">' + 
					'<button id="config_ui_force_interview" class="intl geek"></button>' +
					'<button id="config_ui_show_interview_results" class="intl geek"></button>' +
					'<br/>' +
					'<button id="config_ui_switch_to_geek" class="geek">' + $.translate('config_ui_switch_to_geek') + '</button>' +
				'<div>' + 
			'</div>').translate();

	div_cont.empty();
	div_cont.append(selectxml_cont).append(description_cont).append(assoc_cont).append(config_cont).append(switchall_cont).append(protection_cont).append(wakeup_cont).append(import_export_save_config_cont).append(apply_cont).append(advanced_xml_cont);

	// notifications about deviation of config from zw: markNotification(nodeId);
	
	div_cont.find('#config_ui_request_node_info').bind('click', function() { runCmd('devices[' + nodeId + '].RequestNodeInformation()'); });
	div_cont.find('#config_ui_force_interview').bind('click', function() { runCmd('devices[' + nodeId + '].InterviewForce()'); });
	div_cont.find('#config_ui_show_interview_results').bind('click', function() { showInterviewResults(nodeId); });
	div_cont.find('#config_ui_select_xml').bind('click', function() { showSelectZDDX(nodeId); });
	div_cont.find('#config_ui_configure_like_other').bind('click', function() { copyConfigFromOtherDevice(nodeId); });
	div_cont.find('#config_ui_import_configuration').bind('click', function() { importConfigFromFile(nodeId); });
	div_cont.find('#config_ui_delete_device_config').bind('click', function() { confirm_dialog($.translate('are_you_sure_delete_device_config'), $.translate('config_ui_delete_device_config'), function() { delete_device_config(nodeId); showConfigUISimpleDevice(nodeId); }); });
	div_cont.find('#config_ui_switch_to_geek').bind('click', function() { showConfigUIGeekDevice(nodeId); });
	div_cont.find('#config_ui_show_configuration_xml_link').bind('click', function() { showConfigToSave(nodeId); return false; });
	div_cont.find('#apply_config').bind('click', ConfigUIDataApply);
	div_cont.find('.cut_link').bind('click', function() { if ($(this).next().is(':visible')) $(this).next().fadeOut(); else $(this).next().fadeIn(); });

	external_links(div_cont);

	div_cont.find('.device_assocgrp').droppable({
		scope: "device_icon",
		drop: function(event, ui) {
			$(this).removeClass('hilight').removeClass('hilight_deny');
			var alreadyInAssoc = $(this).find('.device_icon[device='+ui.draggable.attr('device')+']');
			if ($(this).attr('device') != ui.draggable.attr('device') && $(this).find('.device_icon').size() < parseInt($(this).attr('assocgrpmax'), 10) && (alreadyInAssoc.size() == 0 || alreadyInAssoc.hasClass("inZWaveNotInConfig")))
				addAssocDeviceIconToConfig.call(this, $(this).attr("device"), $(this).attr("assocgrp"), parseInt(ui.draggable.attr("device"), 10));
			else {
				ui.draggable.draggable('option', 'revert', true); // to revert the draggable
				setTimeout(function() { ui.draggable.draggable('option', 'revert', false); }, 10); // and to reset this option to false
			}
		},
		over: function(event, ui) {
			var alreadyInAssoc = $(this).find('.device_icon[device='+ui.draggable.attr('device')+']');
			if ($(this).attr('device') != ui.draggable.attr('device') && $(this).find('.device_icon').size() < parseInt($(this).attr('assocgrpmax'), 10) && (alreadyInAssoc.size() == 0 || alreadyInAssoc.hasClass("inZWaveNotInConfig"))) {
				$(this).addClass('hilight');
			} else {
				if ($(this).attr('device') == ui.draggable.attr('device'))
					$.notify({message: $.translate('assoc_cant_self')});
				if ($(this).find('.device_icon').size() >= parseInt($(this).attr('assocgrpmax'), 10))
					$.notify({message: $.translate('assoc_max_nodes_reached')});
				$(this).addClass('hilight_deny');
			}
		},
		out: function(event, ui) {
			$(this).removeClass('hilight').removeClass('hilight_deny');
		}
	});

	div_cont.bindPathNoEval('devices[' + nodeId + '].data.ZDDXMLFile', function() { showConfigUISimpleDevice(nodeId); });

	div_cont.translate(); // translate() should be before button() to have correct button size
	div_cont.find('button').button();
};

function showConfigUIGeekDevice(nodeId) {
	var node = ZWaveAPIData.devices[nodeId];
	var ConfigUIData = $(config_conf);

	var div_cont = $('#deviceconfiguration').unbindPath();

	var deviceConfigs_cont = $('<div class="geek_device_configuration_list" device="' + nodeId + '"></div>');
	deviceConfigs_cont.get(0).node = document.createElement('deviceconfiguration'); // a hack to let save_enable guess the tab we are in
	
	var _default_instance = 0;
	var _default_commandClass = '0';
	var _default_command = '';
	var _default_parameter = '[0]';

	var group=++selectGroup;
	var geekNewConfig = $(
			'<div><h4>' + $.translate('geek_new_config') + '</h4>' + 
				'<span class="value instance geek_new_config dontaddinvalid" device="'+nodeId+'" value="' + _default_instance + '" group="'+group+'"></span>' +
				'<span class="value commandclass geek_new_config dontaddinvalid" value="' + _default_commandClass + '" group="'+group+'"></span>' +
				'<span class="value command geek_new_config dontaddinvalid" value="' + _default_command + '" group="'+group+'"></span>' +
				'<div class="parameter"><span class="subtagname"></span><span class="parameter dontaddinvalid" value="'+_default_parameter+'" group="'+group+'"></span></div><br/>' +
				'<button id="config_ui_add_geek_new_config">' + $.translate('config_ui_add_geek_new_config') + '</button>' +
			'</div>');

	var nC = document.createElement('new_deviceconfiguration'); // new element to store all the data
	nC.setAttribute('id', nodeId);
	nC.setAttribute('instance', _default_instance);
	nC.setAttribute('commandclass', _default_commandClass);
	nC.setAttribute('command', _default_command);
	nC.setAttribute('parameter', _default_parameter);
	geekNewConfig.get(0).node = nC

	var select=instances_htmlSelect(geekNewConfig.find('.value.instance.geek_new_config'), nodeId);

	select_change.call(select);

	(function(nodeId) {
		geekNewConfig.find('#config_ui_add_geek_new_config').bind('click', function() {
				var node = $(parent_node(this));
				ConfigUIGeekAppend(nodeId, parseInt(node.attr('instance'), 10), parseInt(node.attr('commandclass'), 16), node.attr('command'), node.attr('parameter'));
			}
		);
	})(nodeId);
	
	var nogeek_cont = $('<div><button id="config_ui_switch_to_simple">' + $.translate('config_ui_switch_to_simple') + '</button></div>');
	
	div_cont.empty();
	div_cont.append(deviceConfigs_cont).append(geekNewConfig).append(nogeek_cont);

	ConfigUIData.find("devices deviceconfiguration[id=" + nodeId + "]").each(function (i, deviceChange_html) {
		deviceChange = $(deviceChange_html)
		ConfigUIGeekAppend(nodeId, parseInt(deviceChange.attr('instance'), 10), parseInt(deviceChange.attr('commandclass'), 16), deviceChange.attr('command'), deviceChange.attr('parameter'))
	});
	
	div_cont.find('#config_ui_switch_to_simple').bind('click', function() { showConfigUISimpleDevice(nodeId); });

	div_cont.find('button').button();
};

function showConfigUIUnregisteredDevice() {
	var ConfigUIData = $(config_conf);

	var div_cont = $('#deviceconfiguration').unbindPath().empty();

	$.each(array_unique($.map(ConfigUIData.find('devices deviceconfiguration'), function(el) { return $(el).attr('id'); })).sort(), function(index, nodeId) {
		if (!(nodeId in ZWaveAPIData.devices)) {
			var deviceConfigs_cont = $('<div class="geek_device_configuration_list" device="' + nodeId + '"><h4>' + device_name(nodeId) + '</h4></div>');
			deviceConfigs_cont.get(0).node = document.createElement('deviceconfiguration'); // a hack to let save_enable guess the tab we are in
			div_cont.append(deviceConfigs_cont);
			ConfigUIData.find('devices deviceconfiguration[id=' + nodeId + ']').each(function (i, deviceChange_html) {
				var deviceChange = $(deviceChange_html)
				ConfigUIGeekAppend(nodeId, parseInt(deviceChange.attr('instance'), 10), parseInt(deviceChange.attr('commandclass'), 16), deviceChange.attr('command'), deviceChange.attr('parameter'))
			});
		}
	});
	
	div_cont.find('button').button();
};


function showConfigUI() {
	$('#deviceconfiguration').empty('');
	$('#devices_icons_list').bindPath('devices', function() {
		$(this).html(generateDeviceIconList({includeController: true, includeVirtuals: true, includingBroadcast: true, addUnregistered: true}));
		$(this).find('.device_icon').each(function() {
			var nodeId = parseInt($(this).attr('device'), 10);
			$(this).bind('click', function(event) {
				device_select.call(this, event);
				if (nodeId != 255 && nodeId != ZWaveAPIData.controller.data.nodeId.value && (ZWaveAPIData.devices[nodeId] === undefined || !ZWaveAPIData.devices[nodeId].data.isVirtual.value)) {
					$('#deviceconfiguration').attr('device', nodeId);
					try {
						if (nodeId != 0)
							showConfigUISimpleDevice(nodeId);
						else
							showConfigUIUnregisteredDevice();
						$('#deviceconfiguration_toolbar .apply').button('enable');
					} catch(e) {
						error_msg("error_in_showConfigUI", e);
					}
				} else {
					$('#deviceconfiguration').empty().append($('<div></div>').attr('id', nodeId == 255 ? 'no_config_broadcast' : 'no_config_controller').addClass('intl nothingtodisplay').translate());
					$('#deviceconfiguration_toolbar .apply').button('disable');
				}
			})
			.bindPathNoEval('devices[' + nodeId +'],devices[' + nodeId +'].instances,devices[' + nodeId +'].instances[*],devices[' + nodeId +'].instances[*].commandClasses,devices[' + nodeId +'].instances[*].commandClasses[*]', function() {
				// update the interface if the updated device is currently shown
				if ($(this).hasClass('ui-selected'))
					$(this).click();
			})
			.draggable({ delay: 100, scope: "device_icon", containment: "document", zIndex: 2000, helper: "clone", revert: false });
		});
		selectedDevice_arrayRestore();
	});
};

function hideConfigUI() {
	$('#deviceconfiguration').closest('table').find('#devices_icons_list').unbindPath().empty(); // remove devices list if any (it might move into another tab)
	$('#deviceconfiguration').unbindPath().empty();
};

function showRawDeviceInterface(nodeId) {
	tbl = $('<table></table>');

	var instancesCount = 0;
	$.each(ZWaveAPIData.devices[nodeId].instances, function(instanceId, instance) {
		var commandClassesCount = 0;
		var instance_td = $('<td></td>');
		$.each(instance.commandClasses, function(ccId, commandClass) {
			var methodsCount = 0;
			var methods = getMethodSpec(nodeId, instanceId, ccId, null)
			$.each(methods, function(method, params) {
				instancesCount++;
				commandClassesCount++;
				methodsCount++;
				var params = $('<span></span>').addClass('value parameter').attr('value', '[' + repr_array(method_defaultValues(methods[method])) + ']');
				var params_div = $('<div></div>').append($('<span></span>').addClass('subtagname')).append(params); 
				method_gui.call(params.get(0), {
					device: nodeId,
					instance: instanceId,
					commandclass: ccId,
					method: method,
					immediate: true
				});
				params.find('button').button();
				tbl.append(params_div.wrap('<td></td>').parent().wrap('<tr></tr>').parent().prepend('<td>' + method +  '</td>'));
			});
			tbl.find('tr:nth-child(' + (instancesCount - methodsCount + 1) + ')').prepend('<td rowspan="' + methodsCount + '"><a href="#" instance="' + instanceId + '" commandClass="' + ccId + '">' + commandClass.name + '</a></td>');
			(function(nodeId, instanceId, ccId) { tbl.find('a[instance=' + instanceId + '][commandClass=' + ccId + ']').bind('click', function() { showDataHolder(ZWaveAPIData.devices[nodeId].instances[instanceId].commandClasses[ccId].data) });})(nodeId, instanceId, ccId);
		});
		tbl.find('tr:nth-child(' + (instancesCount - commandClassesCount + 1) + ')').prepend('<td rowspan="' + commandClassesCount + '"><a href="#" instance="' + instanceId + '">' + instanceId + '</a></td>');
		(function(nodeId, instanceId) { tbl.find('a[instance=' + instanceId + ']:not([commandClass])').bind('click', function() { showDataHolder(ZWaveAPIData.devices[nodeId].instances[instanceId].data) });})(nodeId, instanceId);
	});
	tbl.prepend('<tr><td>Instance</td><td>CommandClass</td><td>Command</td><td>Parameter</td></tr>');
	$('#raw_devices_interface_div').empty();
	$('#raw_devices_interface_div').append(tbl);
	$('#raw_devices_interface_div').find('button').button();
};

function showRawDevicesInterface() {
	$('#devices_icons_list').bindPath('devices', function() {
		$(this).html(generateDeviceIconList({includeController: false, includeVirtuals: true, includingBroadcast: true, addUnregistered: false}));
		$(this).find('.device_icon')
			.bind('click', function(event) {
				var nodeId = parseInt($(this).attr('device'), 10);
				device_select.call(this, event);
				showRawDeviceInterface(nodeId);
			});
		selectedDevice_arrayRestore();
	});
};

function hideRawDevicesInterface() {
	$('#raw_devices_interface_div').closest('table').find('#devices_icons_list').unbindPath().empty(); // remove devices list if any (it might move into another tab)
	$('#raw_devices_interface_div').unbindPath().empty();
};

// ---------------------

// Removes association Set command from XML, adds Remove command, removes device from the list or marks it as gray (meaning that assoc is only present in the device)
function removeAssocDeviceIconFromConfig(device, assocGroup, assocDevice) {
	$(config_conf).find('devices deviceconfiguration[id=' + device + '][instance=0][commandclass=85][command=Set][parameter="[' + assocGroup + ',' + assocDevice + ']"]').remove();
	addAssocTagToConfig(device, assocGroup, assocDevice, 'Remove');
	save_enable(this);
	addRemoveDeviceIconToAssocGroupBox(device, assocGroup, assocDevice);
};

// Adds association to XML and change style from inZWaveNotInConfig to inConfigAndInZWave
function addAssocDeviceIconToConfig(device, assocGroup, assocDevice) {
	addAssocTagToConfig(device, assocGroup, assocDevice, 'Set');
	save_enable(this);
	addRemoveDeviceIconToAssocGroupBox(device, assocGroup, assocDevice);
};

// add association group container. This function works only with icons, it does not work with XML.
function addRemoveDeviceIconToAssocGroupBox(deviceId, groupId, nodeId, grp_cont) {
	var in_ZW = in_array(nodeId, ZWaveAPIData.devices[deviceId].instances[0].commandClasses[0x85].data[groupId].nodes.value);
	var in_XML = ConfigUIDataIsPresent(deviceId, 0, 0x85, 'Set', '[' + groupId + ',' + nodeId + ']');

	if (!grp_cont)
		var grp_cont = $('.device_assocgrp[device=' + deviceId + '][assocgrp=' + groupId + ']');
	else
		grp_cont = grp_cont.filter('[assocgrp=' + groupId + ']');
	if (grp_cont.size() == 0) return; // no group container
	if (!grp_cont.find('.device_icon[device=' + nodeId + ']').size())
		if (!in_XML && !in_ZW)
			return; // the icons does not exist, nothing to delete
		else
			grp_cont.append(device_icon(nodeId, true).addClass('pointer')); // icon added
	var node_in_grp = grp_cont.find('.device_icon[device=' + nodeId + ']');
	if (in_XML && in_ZW) // inConfigAndInZWave
		node_in_grp.removeClass("inZWaveNotInConfig").removeClass("inConfigNotInZWave").addClass("inConfigAndInZWave").dblclick(function () { removeAssocDeviceIconFromConfig.call(this, $(this).parent().attr("device"), $(this).parent().attr("assocgrp"), parseInt($(this).attr("device"), 10)); });
	else if (!in_ZW && in_XML) // inConfigNotInZWave
		node_in_grp.removeClass("inConfigAndInZWave").removeClass("inZWaveNotInConfig").addClass("inConfigNotInZWave").dblclick(function () { removeAssocDeviceIconFromConfig.call(this, $(this).parent().attr("device"), $(this).parent().attr("assocgrp"), parseInt($(this).attr("device"), 10)); });
	else if (!in_XML && in_ZW) // inZWaveNotInConfig
		node_in_grp.removeClass("inConfigAndInZWave").removeClass("inConfigNotInZWave").addClass("inZWaveNotInConfig").dblclick(function () { addAssocDeviceIconToConfig.call(this, $(this).parent().attr("device"), $(this).parent().attr("assocgrp"), parseInt($(this).attr("device"), 10)); });
	else // remove
		node_in_grp.remove();
};

// Adds association command to XML
function addAssocTagToConfig(device, assocGroup, assocDevice, cmd) {
	ConfigUIDataAppend(device, 0, 0x85, cmd, [assocGroup, assocDevice]);
};

// Add new deviceconfiguration tag to config
function ConfigUIDataAppend(device, instance, commandClass, command, parameters) {
	if (typeof(parameters) != 'string')
		parameters = '[' + parameters.toString() + ']';

	if (ConfigUIDataIsPresent(device, instance, commandClass, command, parameters))
		return;

	var config_el = document.createElement('deviceconfiguration');
	config_el.setAttribute('id', device.toString());
	config_el.setAttribute('instance', instance.toString());
	config_el.setAttribute('commandclass', commandClass.toString(16));
	config_el.setAttribute('command', command);
	config_el.setAttribute('parameter', parameters);
	$(config_conf).find('devices').append(config_el);
};

// Remove deviceconfiguration tag from config
function ConfigUIDataDelete(device, instance, commandClass, command, parameters) {
	if (typeof(parameters) != 'string')
		parameters = '[' + parameters.toString() + ']';
	$(config_conf).find('devices deviceconfiguration[id=' + device.toString() + '][instance=' + instance.toString() + '][commandclass=' + commandClass.toString(16) + '][command=' + command + '][parameter="' + parameters + '"]').remove()
};

// Returns true if configurations with same parameters is already present in config
function ConfigUIDataIsPresent(device, instance, commandClass, command, parameters) {
	return $(config_conf).find('devices deviceconfiguration[id=' + device.toString() + '][instance=' + instance.toString() + '][commandclass=' + commandClass.toString(16) + '][command=' + command + '][parameter="' + parameters + '"]').size() > 0;
};

// Add new item to the list of configs in geek mode
function ConfigUIGeekAppend(nodeId, instance, ccId, command, parameters) {
	if (typeof(parameters) == 'undefined')
		parameters = '[]';
	else if (typeof(parameters) != 'string')
		parameters = '[' + parameters.toString() + ']';
	
	// append to config_conf tree
	if (!ConfigUIDataIsPresent(nodeId, instance, ccId, command, parameters)) {
		ConfigUIDataAppend(nodeId, instance, ccId, command, parameters);
		save_enable($('.geek_device_configuration_list[device=' + nodeId + ']')[0]);
	};
	
	var ccName = $.translate('not_registered_command_class') + ' ' + '0x' +('00' + ccId.toString(16)).slice(-2);
	if ((nodeId in ZWaveAPIData.devices) && (instance in ZWaveAPIData.devices[nodeId].instances) && (ccId in ZWaveAPIData.devices[nodeId].instances[instance].commandClasses))
		ccName = ZWaveAPIData.devices[nodeId].instances[instance].commandClasses[ccId].name;

	// append to interface
	var presentConfigInput = $('.geek_device_configuration_list[device=' + nodeId + ']').find('input[device=' + nodeId.toString() + '][instance=' + instance.toString() + '][commandclass=' + ccId.toString(16) + '][command=' + command + '][parameter="' + parameters + '"]');
	if (presentConfigInput.size() == 0) {
		var deviceConfig = $('<label><input type="checkbox" checked="true" device="' + nodeId.toString() + '" instance="' + instance.toString() + '" commandclass="' + ccId.toString(16) + '" command="' + command + '" parameter="' + parameters + '" />Instance ' + instance.toString() + ', ' + ccName + ', ' + command + parameters + '</label><br />');
		deviceConfig.find('input').bind('change', function() {
			if ($(this).is(':checked')) {
				ConfigUIDataAppend(nodeId, instance, ccId, command, parameters);
			} else {
				ConfigUIDataDelete(nodeId, instance, ccId, command, parameters);
			};
			save_enable(this);
		});
		
		$('.geek_device_configuration_list[device=' + nodeId + ']').append(deviceConfig);
	} else
		presentConfigInput.attr('checked', 'checked');
};


// Apply all configs for a device
function ConfigUIDataApply() {
	var device = $('#deviceconfiguration').attr('device');
	if (device == 255 || device == ZWaveAPIData.controller.data.nodeId.value)
		return;
	$(config_conf).find('devices deviceconfiguration' + ((device) ? ('[id=' + device.toString() + ']') : '')).each(function (index, cfg_) {
		cfg = $(cfg_);
		runCmd('devices[' + cfg.attr('id') + '].instances[' + cfg.attr('instance') + '].commandClasses[' + parseInt(cfg.attr('commandclass'), 16).toString() + '].' + cfg.attr('command') + '(' + cfg.attr('parameter').slice(1, -1) + ')');
	});
};

// ---------------------------

function updateDialogWindowHeight(height) {
	$(this).height(height - $(this).closest('.ui-dialog').find('.ui-dialog-titlebar').outerHeight(true) - ($(this).outerHeight() - $(this).height()) /* padding size */);
};

var timerQueueUpdate = null; // to add/remove queue interval timer
var running_getQueueUpdate = false; // in case request would take more than interval between subsequent requests
// Open queue update window and set update timers
function openQueueWindow() {
	$('#inspect_queue').dialog({
		modal: false,
		closeOnEscape: false,

		title: $.translate('nm_inspect_queue_title'),
		
		open: function() {
			var bh = $('body').height();
			var bw = $('body').width();
			$(this).closest('.ui-dialog').height(bh*0.3).width(bw).offset({top: bh*0.7, left: 0});
			updateDialogWindowHeight.call(this, bh*0.3);
			if (!timerQueueUpdate) {
				getQueueUpdate();
				timerQueueUpdate = setInterval(getQueueUpdate, 1000);
			};
		},

		close: function() {
			if (timerQueueUpdate) {
				clearInterval(timerQueueUpdate);
				timerQueueUpdate = null;
			};
			running_getQueueUpdate = false;
		},

		resizeStop: function(event, ui) {
			updateDialogWindowHeight.call(this, ui.size.height);
		}
	});
};

// Get Queue updates
function getQueueUpdate() {
	if (running_getQueueUpdate)
		return;
	running_getQueueUpdate = true; // begin task
	$.postJSON('/ZWaveAPI/InspectQueue', function (data, status) {
		if (status != 'success' || data == null) {
			running_getQueueUpdate = false; // task done
			return;
		};
		var trs = '';
		$.each(data, function (jobIndex, job) {
			var buff = '';
			for (var b in job[5]) {
				buff += job[5][b].toString(16) + ' ';
			};
			
			var progress;
			if (job[4] === null) {
				progress = '';
			} else if (typeof(job[4]) == 'string') {
				progress = job[4].replace(/\n/g, '<br/>')
			} else {
				job[4].join('<br />');
			}
			
			trs += 
				'<tr>' +
					'<td>' + job[1][0] + '</td>' +
					'<td>' + (job[1][1]?"W":" ") + '</td>' +
					'<td>' + (job[1][2]?"S":" ") + '</td>' + 
					'<td>' + (job[1][3]?"E":" ") + '</td>' + 
					'<td>' + (job[1][4]?"D":" ") + '</td>' + 
					'<td>' + (job[1][5]?(job[1][6]?"+":"-"):" ") + '</td>' + 
					'<td>' + (job[1][7]?(job[1][8]?"+":"-"):" ") + '</td>' + 
					'<td>' + (job[1][9]?(job[1][10]?"+":"-"):" ") + '</td>' + 
					'<td>' + parseFloat(job[0]).toFixed(2) + '</td>' + 
					'<td>' + job[2] + '</td>' + 
					'<td class="alignleft">' + job[3] + '</td>' + 
					'<td class="alignleft">' + progress + '</td>' + 
					'<td class="alignleft">' + buff + '</td>' + 
				'</tr>\n';
		});
		if (trs == '')
			trs = '<tr><td colspan="12"><i>' + $.translate('inspect_queue_empty') + '</i></td></tr>';
		$('#inspect_queue_len').html('Queue length: ' + $.objLen(data));
		$('#inspect_queue_table_body').html(trs);

		running_getQueueUpdate = false; // task done
	});
};

/*
	Timing visualization
	CP 02.12.2010 + PS 17.12.2010

	NB:
	I like to mark and count not delivered wakeup_no_more_info packets different than other failed communication, since its a different reason.
	For this the data structure should have a item marking this special type of packet. Worth ??
*/
/*
function showCommTiming() {
	$.postJSON('/ZWaveAPI/Run/jobDeliveryTimes', function (data, status) {
		if (status != 'success' || data == null)
			return;
		var out = '';
		$.each(ZWaveAPIData.devices, function (nodeId, node) {
			var s1 = 0, s2 = 0, s3 = 0;
			$.each(data, function (i, arr) {
				if (arr[0] == nodeId) {
					var d = (arr[3]-arr[2])*1000;
					if (!arr[1])
						s3++;
					else if (d < 100)
						s1++;
					else if (d > 100 && node.data.sensor1000.value)
						s1++;
					else if (d > 100 && node.data.sensor250.value)
						s1++;						
					else
						s2++;
				}
			});
			var num = s1+s2+s3;
			if (num > 0) {
				var type;
				if (node.data.isListening.value)
					type = 'mains';
				else if (node.data.sensor1000.value)
					type = 'flirs1000';
				else if (node.data.sensor250.value)
					type = 'flirs250';
				else
					type = 'battery';

				out +=
					'<tr>' +
						'<td>Node ' + nodeId + '</td>' +
						'<td>' + type + '</td>' +
						'<td>' + num + ' pkts  ' + 
						//'(' +node.data.countSuccess.value +'/' + node.data.count.Failed.value +'</td>' +
						'<td>' + parseInt(s1/num*100) + '%</td>' +
						'<td>' + parseInt(s2/num*100) + '%</td>' +
						'<td>' + parseInt(s3/num*100) + '%</td>' +
						'<td>';

				// show last m packets
				var m = 10;
				for (var i = data.length-1; i && m; i--) {
					if (data[i][0] == nodeId) {
						m--;
						var k = 100 * (data[i][3]-data[i][2]); // delta t in 10ms units

						var packet_class = '';
						if (!data[i][1])
							packet_class = 'comm_timing_bad';
						else if (k<10)
							packet_class += 'comm_timing_good';
						else
							packet_class += 'comm_timing_soso';

						out += '<span class="' + packet_class + '">' + parseInt(k) + '</span> ';
					}
				};
				out += '</td></tr>';
			}
		});
		$('#comm_timing_table_body').html(out);
		$('#comm_timing_div').dialog({
			modal: false,
			width: 600,
			height: 400,
			title: $.translate('comm_timing_title'),
			open: function() {
				dialog_init(this);
			},
			buttons: {
				reset : function() {
					runCmd('jobDeliveryTimes.Update([])');
					$(this).dialog('close');
				},
				ok : function() {
					$(this).dialog('close');
				}
			}
		});
	});
};
*/

// -----------------------------------------

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

// Return span with current date in smart format and class="red" if the data is outdated or class="" if up to date
function getUpdated(data) {
	return '<span class="' + ((data.updateTime > data.invalidateTime) ?'':'red') + '">' + getTime(data.updateTime, '?') + '</span>';
};
