var config;
var config0;

function config_parse(xml) {
	if (xml.childNodes.length!=1 || xml.childNodes[0].tagName.toLowerCase()!='config') {
		error_msg($.translate('syntax_error')+' '+xml.childNodes[0].tagName,xml.childNodes[0]);
		return;
	};
	config=xml.childNodes[0];
	if (config.tagName.toLowerCase()!='config') {
		error_msg($.translate('syntax_error')+' '+config.tagName,config);
		return;
	}

	make_children(config);
	config0=config.cloneNode(true);
	for (var i=0; i<config._children.length; ++i) {
		var section=config._children[i];
		switch(section.tagName.toLowerCase()) {
			case 'coordinates':
				config.coordinates=section;
				break;
			case 'areas':
				config.areas=section;
				break;
			case 'scenes':
				config.scenes=section;
				break;
			case 'climates':
				config.climates=section;
				break;
			case 'rules':
				config.rules=section;
				break;
			case 'schedules':
				config.schedules=section;
				break;
			case 'devices':
				config.devices=section;
				break;
			default:
				error_msg($.translate('syntax_error')+' '+section.tagName,section);
				break;
		}
	};
};


// build _children array because only firefox has node.children array
function make_children(node) {

	if (false && node._children && typeof node._children == "object") {
		if (!node.myChildren)
			return;

		node._children.splice(0,node._children.length);

	} else {
		node.myChildren=true;
		node._children=new Array();
	};

	if (!node.childNodes)
		return;

	for (var i=0; i<node.childNodes.length; ++i) {
		if (node.childNodes[i].tagName) {
			node._children.push(node.childNodes[i]);
			make_children(node.childNodes[i]);
		}
	}
};


function config_load() {
	$.ajax({
		type: 'GET',
		url: '/config/Rules.xml',
		dataType: 'xml',
		async: false,
		success: config_parse,
		error: function(XMLHttpRequest, textStatus, errorThrown){
			error_msg('cannot_read'+' Rules.xml: ' + (errorThrown ? errorThrown : XMLHttpRequest.responseText));
		}
	});
};

/*
	Debug function
*/ /*
function print_r(x, max, sep, l) {
	l = l || 0;
	max = max || 10;
	sep = sep || ' ';
	if (l > max) { return "[WARNING: Too much recursion]\n";}
	var
		i, r = '', t = typeof x, tab = '';
	if (x === null) {
		r += "(null)\n";
	} else if (t == 'object') {
		l++;
		for (i = 0; i < l; i++) { tab += sep;}
		if (x && x.length) { t = 'array';}
		r += '(' + t + ") :\n";
		for (i in x) {
			try {
				r += tab + '[' + i + '] : ' + print_r(x[i], max, sep, (l + 1));
			} catch(e) {
				return "[ERROR: " + e + "]\n";
			}
		}
	} else {
		if (t == 'string') { if (x == '') { x = '(empty)'; }}
		r += '(' + t + ') ' + x + "\n";
	}
	return r;
}
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
		url: url,
		data: data,
		dataType: 'json',
		success: callback,
		error: callback,
		async: (sync!=true)
	});
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
	setInterval(getDataUpdate, 5000);
};

function error_msg(msg) {
	alert(msg);
}

function alert_dialog(msg, title) {
	alert(title ? (title + ': ') : '' + msg);
}

// dumb function instead of jquery.translation
$.translate = function(msg) {
	return msg;
}

/*
	run ZWaveAPI command via HTTP POST
*/
function runCmd(cmd, success_cbk) {
	$.postJSON('/ZWaveAPI/Run/'+ cmd, function (data, status) {
		if (status == 'success' || status == '') {
			if (success_cbk) success_cbk();
			if (data) console.log(data);
		} else
			error_msg('runCmd_failed');
	});
	return 'sent';
};
window.runCmd = runCmd; // export into the world

/*
	Get updates data from ZWaveAPI via HTTP POST
*/
var fbug; // to disable requests in debug mode
var running_getDataUpdate = false; // in case request would take more than interval between subsequent requests

function getDataUpdate(sync) {
	if (!fbug && !running_getDataUpdate) {
		running_getDataUpdate = true; // begin task
		$.postJSON('/ZWaveAPI/Data/' + ZWaveAPIData.updateTime, handlerDataUpdate, sync);
	}
};

function handlerDataUpdate(data, status) {
	if (status != 'success' || data == null) {
		running_getDataUpdate = false; // task done
		return;
	};
	try {
		// handle data
		$.each(data, function (path, obj) {
			var pobj = ZWaveAPIData;
			var pe_arr = path.split('.');
			for (var pe in pe_arr.slice(0, -1))
				pobj = pobj[pe_arr[pe]];
			pobj[pe_arr.slice(-1)] = obj;

			// restrict UI updates only to some paths
			if (
					(new RegExp('^devices\\..*\\.instances\\..*\\.commandClasses\\.(37|38|48|49|50|67|98|128)\\.data.*$')).test(path) || 
					(new RegExp('^areas\\.data\\..*\\..*$')).test(path)
					)
				$.triggerPath.update(path);
		});
	} catch(err) {
		error_msg('error_in_DataUpdate', err.stack);
	};
	
	running_getDataUpdate = false; // task done
};


$(document).ready(function() {
	/* translation
	lang=$.cookie('language');
	$('.intl').language_set(lang?lang:'en');

	// localize jquery.dateformat module
	if (dateFormat) {
		for (d in dateFormat.i18n.dayNames)
			dateFormat.i18n.dayNames[d] = $.translate(dateFormat.i18n.dayNames[d])
		for (m in dateFormat.i18n.monthNames)
			dateFormat.i18n.monthNames[m] = $.translate(dateFormat.i18n.monthNames[m])
	};
	*/

	// to workaround problem with jQuery mobile initialization when accessed by URL with hash (#allsensors, happens after reload)
	if (document.baseURI != document.URL) {
		document.location.href = document.baseURI;
		return;
	}

	config_load();

	$.triggerPath.init(ZWaveAPIData);
	ZWaveAPIDataUpdate_init();

	initDashboard();
});	