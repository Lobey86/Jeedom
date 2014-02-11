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

/*
	POST JSON function
*/
$.postJSON = function(url, data, callback, sync) {

	// shift arguments if data argument was omited
	if ( jQuery.isFunction( data ) ) {
		sync = sync || callback;
		callback = data;
		data = {};
	};
	$.ajax({
		type: 'POST',
		url: (server_host ? ('http://'+server_host) : '') + url,
		data: data,
		dataType: 'json',
		success: callback,
		error: callback,
		async: (sync!=true)
	});
};

// this allows to switch to different server on the fly
var server_host = null;
window.ZWayServerChange = function(host) {
	server_host = host;
	ZWaveAPIData.updateTime = 0;
};

/*
	POST XML function
*/
$.postXML = function(url, data, callback, sync) {

	// shift arguments if data argument was omited
	if ( jQuery.isFunction( data ) ) {
		sync = sync || callback;
		callback = data;
		data = {};
	};

	$.ajax({
		type: 'POST',
		url: url,
		data: data,
		dataType: 'xml',
		success: callback,
		error: callback,	
		async: (sync!=true)
	});
};
	
/*
	len function
*/
$.objLen = function objLen(obj) { var l = 0; for (name in obj) l++; return l; };

/*
	Array unique
*/
function array_unique(arr) {
	var newArray = new Array();

	label:for (var i=0; i<arr.length;i++ ) {  
		for (var j=0; j<newArray.length;j++ )
			if (newArray[j] == arr[i]) 
				continue label;
		newArray[newArray.length] = arr[i];
	}
	return newArray;
};

/*
	Test an element in array and returns it's index
*/
function in_array(v, arr, return_index){
	for (i=0; i<arr.length; i++)
		if (arr[i]==v)
			return return_index?i:true;
	return false;
};

/*
	holder for all data
*/
var ZWaveAPIData = { updateTime: 0 };
window.ZWaveAPIData = ZWaveAPIData; // export into the world

/*
	Load data and reload it every X second
*/
function ZWaveAPIDataUpdate_init() {
	getDataUpdate(true);
	setInterval(getDataUpdate, 2000);
};

/*
	Get data holder element
*/
function getDataHolder(data) {
	var r = '<div class="Data">';
	r += '<div class="DataElement">' + data.name+': <font color="' + ((data.updateTime > data.invalidateTime) ? 'green' : 'red') + '">'+((typeof(data.value) !== 'undefined' && data.value != null)?data.value.toString():'None')+'</font>' + ' (' + getUpdated(data) + ')</div>';

	$.each(data, function (key, el) {
		if (key != 'name' && key != 'type' && key != 'updateTime' && key != 'invalidateTime' && key != 'value' && // these are internal values
				key != 'capabilitiesNames') // these make the dialog monstrious
			r += getDataHolder(el);
	});

	r += '</div>';
	return r
};

/*
	Shows data holder
*/
function showDataHolder(data) {	
	$('div.DataHolder').html(getDataHolder(data))
		.css({'max-height': $(document.body).height()-128, height: 'auto'})
		.dialog({
			modal: true,
		       	title: 'Command class data',
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

// #if admin
/*
	Ask for available ZDDX for a device
*/
function showSelectZDDX(nodeId) {
	$.postJSON('/ZWaveAPI/Run/devices[' + nodeId + '].GuessXML()', function (data, status) {
		if (status != 'success') {
			return;
		};

		if (data == null)
			return;
		
		var zddxSorted = data;
		zddxSorted.sort(function(a, b) {return a.score - b.score});
		zddxSorted.reverse();

		var ZDDXFileVar = ZWaveAPIData.devices[nodeId].data.ZDDXMLFile;
		var ZDDXFileName = (typeof(ZDDXFileVar) != "undefined") ? ZDDXFileVar.value : '';

		var options = '';
		for (var zddxIndex in zddxSorted)
			options += '<option deviceimage="' + (zddxSorted[zddxIndex].deviceImage ? zddxSorted[zddxIndex].deviceImage : 'pics/no_device_image.jpg') + '" value="' + zddxSorted[zddxIndex].fileName + '"' + (ZDDXFileName == zddxSorted[zddxIndex][0] ? ' selected' : '') + '>' + ((zddxSorted[zddxIndex].brandName == '' || zddxSorted[zddxIndex].productName == '')?('Unnamed ZDDX: ' + zddxSorted[zddxIndex].fileName):(zddxSorted[zddxIndex].brandName + ' ' + zddxSorted[zddxIndex].productName)) + '</option>\n';
		
		$('#custom_dialog').html($.translate('select_zddx_help') + '<br />' + '<select id="select_zddx_select">' + options + '</select><br /><img src="" id="select_zddx_image"/>');
		$('#select_zddx_select').bind('keypress keyup keydown change', function() { $('#select_zddx_image').attr('src', $('#select_zddx_select option:selected').attr('deviceimage')); });
		$('#select_zddx_select').change(); // update the image
		$('#custom_dialog')
			.css({'max-height': $(document.body).height()-128})
			.dialog({
				modal: true,
		       		title: $.translate('config_ui_select_xml_title'),
			       	width: 'auto',
				open: function() {
					dialog_init(this);
				},
			       	buttons: {
					cancel : function() {
				       			$(this).dialog('close');
					},

					select : function () {
						var newZDDX = $('#select_zddx_select').val();
						if (typeof(newZDDX) == "undefined")
							return;
						runCmd('devices[' + nodeId + '].LoadXMLFile("' + newZDDX + '")');
						$(this).dialog('close');
					}
				}
			});
	});
};
// #endif admin

/*
	Notify about next node wakeup
*/
//var lastNotification = [];
/*
function nodesCmdWarning(nodeId) {
	if (!nodeId)
		return;
	d = ZWaveAPIData.devices[nodeId];
	var deviceAwakeNote = d.data.isAwake.value ? $.translate('device_is_awake') : '';
	if (d.instances[0].commandClasses[0x84]) {
		lastSleep = parseInt(d.instances[0].commandClasses[0x84].data.lastSleep.value, 10);
		interval = parseInt(d.instances[0].commandClasses[0x84].data.interval.value, 10);
		nextWakeup = lastSleep + interval;
		return deviceAwakeNote + $.tranlate('next_wakeup') + getTime(nextWakeup, $.translate('unknown_next_wakeup'));
		alert("Node "+nodeId+" is a battery device. The command will be executed at "+wt +" or when you manually wakeup the device")
	} else if (d.data.basicType.value == 1)
		return alert("Node "+nodeId+" is a remote control. You need to wakeup the device ot execute this command")
	return null;
};
*/

/*
	run ZWaveAPI command via HTTP POST
*/
function runCmd(cmd, success_cbk) {
	$.postJSON('/ZWaveAPI/Run/'+ cmd, function (data, status) {
		if (status == 'success' || status == '') {
			if (success_cbk) success_cbk();
			if (data) console.log(data);
		} else
			alert_dialog($.translate('runCmd_failed') + ': ' + data.statusText);
	});
	return 'sent';
};
window.runCmd = runCmd; // export into the world

function runJS(cmd, success_cbk) {
	$.postJSON('/JS/Run/' + cmd, function (data, status) {
		if (status == 'success' || status == '') {
			if (success_cbk) success_cbk();
			if (data) console.log(data);
		} else
			alert_dialog($.translate('runJS_failed') + ': ' + data.statusText);
	});
	return 'sent';
};
window.runJS = runJS; // export into the world

/*
	Get updates data from ZWaveAPI via HTTP POST
*/
var fbug; // to disable requests in debug mode
var running_getDataUpdate = false; // in case request would take more than interval between subsequent requests
function getDataUpdate(sync) {
	if (!fbug && !running_getDataUpdate) {
		running_getDataUpdate = true; // begin task
		$('.updateTimeTick').addClass('red');
		$.postJSON('/ZWaveAPI/Data/' + ZWaveAPIData.updateTime, handlerDataUpdate, sync);
	}
};

function handlerDataUpdate(data, status) {
	if (status != 'success' || data == null) {
		running_getDataUpdate = false; // task done
		return;
	};

	// check if we need to remove all bindings to restore them back (in case of huge update)
	var reRender = false;
	if ($.objLen(data) > ($.browser.mobile ? 30 : 100)) {
		alert_dialog($.translate('interface_updating_wait_msg'), $.translate('interface_updating_wait_title'));
		reRender = true;
		var cur_tabId = $('.tab:visible').attr('id');
		select_tab(null); // hide all tabs
		//unbindCommonPaths(); // keep these, since they are not updated during re-rendering

// #if dev
		// do not check for leaving bindings since we do not remove bindings on <triggerpath>
		//if ($(':data(' + $.triggerPath.dataKey + ')').size())
		//	alert_dialog('There are still some bindPath leaving after removing all known bindings. Check this in handlerDataUpdate()');
// #endif dev
	};

	try {
		// handle data
		$.each(data, function (path, obj) {
			var pobj = ZWaveAPIData;
			var pe_arr = path.split('.');
			for (var pe in pe_arr.slice(0, -1))
				pobj = pobj[pe_arr[pe]];
			pobj[pe_arr.slice(-1)] = obj;
			
			$.triggerPath.update(path);
		});
	} catch(err) {
		error_msg($.translate('error_in_DataUpdate'), err.stack);
	} finally {
		// restore back if needed
		if (reRender) {
			alert_dialog_close();
			//bindCommonPaths(); // we did not unbind these before, so no ned to bind
			select_tab(cur_tabId);
		}
	};
	
	running_getDataUpdate = false; // task done

	// update time button. we are doing it here and not using bindPath to save resources
	$('.updateTimeTick').removeClass('red').find('.ui-button-text').html((new Date(parseInt(ZWaveAPIData.updateTime, 10)*1000)).format('HH:MM:ss'));
};

var _ZDDXFiles = {};
// loads and cache ZDDX files from the server
function ZDDX(nodeId) {
	if (!_ZDDXFiles[nodeId]) {
		
		var file = ZWaveAPIData.devices[nodeId].data.ZDDXMLFile.value;
	
		if (file) {
			$.ajax({
				type: 'GET',
				url: '/ZDDX/' + file,
				dataType: 'html',
				async: false,
				success: function(xml) {
					_ZDDXFiles[nodeId] = xml;
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					alert_dialog($.translate('cannot_read') + ' ' + file + ': ' + errorThrown + ((errorThrown && XMLHttpRequest.responseText) ? ', ' : '')  + XMLHttpRequest.responseText);
					_ZDDXFiles[nodeId] = "";
				}
			});
		} else {
			_ZDDXFiles[nodeId] = "";
		}
	}

	var zddx = $(_ZDDXFiles[nodeId]);
	zddx.find('lang').parent().each(function() {
		var _l;
		if ($(this).find('lang[xml\\:lang=' + lang + ']').length)
			_l = lang;
		else
			_l = 'en';
		$(this).find('lang[xml\\:lang!=' + _l + ']').remove();
	});
	return zddx;
};
