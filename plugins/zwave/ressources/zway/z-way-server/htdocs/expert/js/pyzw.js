
/*
 *   pyzw.js - PYZW configuration editor
 *   
 *   Copyright (C) 2010 Luc Deschenaux - luc.deschenaux(a)freesurf.ch
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
*/

var htmlSelectStyle={"font-family": "monospace", "font-size": "120%"};
var cache_opacity="0.02";
var region_cache_opacity="0.2";
var _xml;
var lang;
var config;
var config0;
var hilitArea;
var rootArea;
var selectedArea=new Array;
var rootHistory=new Array;
var mapEdit=false;
var areaMenu;
var contextMenu=false;
var eventName=new Array;
var eventInterface=new Array;
var eventDefaultValues=new Array;
///var month=["January","February","March","April","May","June","July","August","September","October","November","December"];
///var weekday=["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
///var crayonBoxColours = new Array('Fuchsia', 'Purple', 'Red', 'FireBrick', 'Tomato', 'OrangeRed', 'Orange', 'Gold', 'Green', 'Turquoise', 'Teal', 'Navy', 'Blue');
///var condlogic=["and","or","not"];
///var collapsible_tag_timeout;
var selectGroup=0;
var _mousedown=new Object;
///var defaultClimateSchedule = [[['8:00', 20]], [['8:00', 20]], [['8:00', 20]], [['8:00', 20]], [['8:00', 20]], [['8:00', 20]], [['8:00', 20]]];

///var fro_name=new Object;
///fro_name.minute=new Array;
///for (var i=0; i<60; ++i) {
///	fro_name.minute[i]=i;
///};
///fro_name.hour=new Array;
///for (var i=0; i<24; ++i) {
///	fro_name.hour[i]=i+"h";
///};
///fro_name.day=new Array;
///for (var i=1; i<32; ++i) {
///	fro_name.day[i]=i;
///};
///fro_name.weekday=new Array;
///for (var i=1; i<7; ++i) {
///	fro_name.weekday[i]=weekday[i-1];
///};
///fro_name.month=new Array;
///for (var i=1; i<13; ++i) {
///	fro_name.month[i]=month[i-1];
///};

if (typeof console != "object") {
	console=new Object; // here and further console would be equal to window.console, since no statment var is used
	console.log=function(){};
};

// Override jQuery.fn.slider and jQuery.fn.draggable to work on touch devices
function setupTouchOverrides() {
	// override slider
	$.fn._slider = $.fn.slider;
	$.fn.slider = function () {
		var ret = $.fn._slider.apply(this, arguments);
		this.each(function() {
			setupTouchHandler(this);
		});
		return ret;
	};

	// override draggable
	$.fn._draggable = $.fn.draggable;
	$.fn.draggable = function () {
		var ret = $.fn._draggable.apply(this, arguments);
		this.each(function() {
			setupTouchHandler(this);
		});
		return ret;
	};
};

// Set up event converters. Should be called for each DOM element (sliders and others) you want to have drag & drop events instead of classical scroll behavior.
function setupTouchHandler(element) {
	element.addEventListener("touchstart", touchHandler, false);
	element.addEventListener("touchmove", touchHandler, false);
	element.addEventListener("touchend", touchHandler, false);
};

// Simulation of mouse events from touch events
function touchHandler(event) {			

	// Mark device as smartphone even if it is not reported by browser
	if (!$.browser.mobile) {
		$.browser.mobile=true;
	};

	var touches = event.changedTouches,
		first = touches[0],
		type = "";
	
	switch(event.type) {
		case "touchstart":
			type = "mousedown";
			break;
		case "touchmove":
			type="mousemove";
			event.preventDefault();
			break;
		case "touchend":
			type="mouseup";
			break;
		default:
			return;
	};
	
	var simulatedEvent = document.createEvent("MouseEvent");
	simulatedEvent.initMouseEvent(type, true, true, window, 1, first.screenX, first.screenY, first.clientX, first.clientY, false, false, false, false, 0, null);
	first.target.dispatchEvent(simulatedEvent);
};

function toolbar_place() {
	var toolbar = $('div.toolbar_container');
	toolbar.css({top: document.documentElement.clientHeight - toolbar.height()});
};

$(document).ready(function() {
	
	if (!($.browser.webkit || $.browser.mozilla)) {
		alert_dialog('Z-Way supports only <a href="http://www.google.com/chrome">Google Chrome</a>, <a href="http://www.apple.com/safari/">Apple Safari</a> and <a href="http://www.mozilla.com/firefox/">Mozilla Firefox</a>.<br/>We apologize.', 'Error loading Z-Way');
		return;
	};

	$.browser.mobile=(navigator.userAgent.search('Mobile')!=-1);

	$(window).bind('mousedown',function(event){
		_mousedown={
			target: event.target,
			pageX: event.pageX,
			pageY: event.pageY,
			timeStamp: event.timeStamp,
			ctrlKey: event.ctrlKey,
			shiftKey: event.shiftKey
		};
		return true;
	});

	setupTouchOverrides(); // setup touch handler

	// use ctrl to display browser contexmenu
	$(document)[0].oncontextmenu = function(event) {
		return (event.ctrlKey);
	};

	lang=$.cookie('language');
	if (!lang) {
		$.cookie('language', 'en');
		lang = 'en';
	};
	$('.intl').language_set(lang);
	
	$('.lang_select a').click(function() {
		lang = $(this).html();
		$('.intl').language_set(lang);
		$.cookie('language', lang);
		alert_dialog($.translate('language_select_reload_interface'), $.translate('language_select'));
	});

	// localize title
	document.title = $.translate('pyzw_title');

	// localize jquery.dateformat module
	if (dateFormat) {
		for (d in dateFormat.i18n.dayNames)
			dateFormat.i18n.dayNames[d] = $.translate(dateFormat.i18n.dayNames[d])
		for (m in dateFormat.i18n.monthNames)
			dateFormat.i18n.monthNames[m] = $.translate(dateFormat.i18n.monthNames[m])
	};

	$('[title]').tooltip();

	// fetch data once before initialization
	getDataUpdate(true);

	if (ZWaveAPIData.controller == undefined || ZWaveAPIData.devices == undefined) {
		alert_dialog($.translate('server_not_started_yet'));
		return false;
	}

	config_load();

	$('#areas, #scenes, #climates, #rules, #schedules').translate();
	
	external_links($('body'));

	tabs_zwave_start(); //should be before loading tabs

	$('.updateTimeTick').bind('click', function() {
		if (running_getDataUpdate)
			confirm_dialog($.translate('are_you_sure_kick_updates'), $.translate('kick_updates'), function () {
				running_getDataUpdate = false;
			});
	});

	$('button.geek_mode').bind('click', toggleGeekMode);
	setGeekMode($.cookie('geek_mode') == '1');

	$('button.show_devices_in_tree').bind('click', toggleShowDevicesInTree);
	setShowDevicesInTree($.cookie('show_devices_in_tree') == '1');

	$('#tabs').jsddm('#tab_areas', function(panel) {
		panelHeight_update();
		rightpanel_resize();

		var tabId=$(panel).attr('id');

		// show corresponding toolbar
		var hasToolbar;	
		$('.toolbar').each(function(){
			if ($(this).hasClass(tabId)) {
				$(this).show();
				hasToolbar=true;
			} else {
				$(this).hide();
			};
		});
		
		if (hasToolbar) {
			// copy the omni toolbar items into toolbar
			$('.toolbar_container .toolbar .omni').appendTo($('.toolbar_container > :visible'));
			$('.toolbar_container').show();
			if ($('.toolbar_container > :visible :visible').size() == 0)
				$('.toolbar_container').hide(); // there is no items in the toolbar - hide it
		} else {
			$('.toolbar_container').hide();
		};

		select_tab(tabId);

		$('#area_list').scrollpos_restore();
		$(panel).find('div.rightpanel').scrollpos_restore();
		$(panel).find('.fullpanel').scrollpos_restore();
		$(panel).find('div.leftpanel').scrollpos_restore();			
	});

	$('#tabs').css({display: 'block'});

	$(window).bind('resize',function() {
		var div=$('ul.ui-tabs-nav').parent();
		div.width(div.parent().width());
		toolbar_place();
		return true;
	});

	$('#area_list').scrollpos_restore();
	$('div.rightpanel').scrollpos_restore();
	$('.fullpanel').scrollpos_restore();
	$('div.leftpanel').scrollpos_restore();

	toolbar_init();
	toolbar_place();

	$('.scroll').scrollpos_save();

	enable_hilighting();
	$('#area_list, #areas').fadeIn();

	areamenu_setup();

	$('.save').bind('click',function(){
		$('input',tab_current()).find(':focus').blur();
		config_save.call(this);
	}).button('disable');

	$(window).bind('beforeunload',function(){
		var unsaved;
		var where = $.makeArray($('.save').map(function() { return (!$(this).attr('disabled')) ? $.translate(parent_attr(this,'tag')) : null}));

		if (where.length > 0) {
			return $.translate('unsaved_changes')+' ('+where.join(', ')+').';
		}
	});

	$(document).bind('dragstart',function(event) {
		return $(event.target).hasClass('ui-draggable');
	});

	try {
		selectedArea_arrayRestore();
	} catch(e) {
		console.log(e);
	};
	
	areaToolbar_update();

	panelHeight_update();
	verticalSeparator_init(function(){
		$('#areas').resize();
	});

	setTimeout(verticalSeparator_restore,1);

	$('#areas').bind('scroll resize',devices_updatePosition);

	$(window).resize(function(){
		panelHeight_update();
		rightpanel_resize();
	});

	bindCommonPaths();

	$(window).bind('resize',function(event) {
		if ($('#tab_areas:visible').size())
			devices_updatePosition();
	});

	deviceIconListMenu_setup();

	///$('.menu_box').live('mousedown',function(event){event.type='contextmenu';$(this).trigger(event)});
	
	ZWaveAPIDataUpdate_init();
	
	$('#loading').remove();

});

function bindCommonPaths() {
	// Create hidden DOM elements for each bindPath
	// TODO // $.triggerPath.bindPathNoEval('areas.data[*].scene,areas.data.[*]', enable_hilighting);
	$.triggerPath.bindPathNoEval('devices', deviceList_update);
	$.triggerPath.bindPathNoEval('devices[*],devices[*].instances,devices[*].instances[*]', function(obj, path) {
		var device = path.split('.')[1]
		if (device != undefined)
			device = parseInt(device, 10);
		instances_htmlSelect_update(device);
	});
	$.triggerPath.bindPathNoEval('devices[*].instances[*].commandClasses,devices[*].instances[*].commandClasses[*]', function(obj, path) {
		var device = path.split('.')[1]
		if (device != undefined)
			device = parseInt(device, 10);
		var instance = path.split('.')[3]
		if (instance != undefined)
			instance = parseInt(instance, 10);
		commandClasses_htmlSelect_update(device, instance);
	});
};

function unbindCommonPaths() {
	$('triggerpath').remove();
};

function custom_map_objects_show() {
	var imgOffset = $('img.map').offset();
	
	custom_map_objects_hide();
	
	if (typeof(custom_map_objects) == "undefined")
		return;
	
	for (i in custom_map_objects) {
		var obj = custom_map_objects[i];
		
		try {
			var icon = obj.icon;
			switch (typeof(icon)) {
				case "string":
					icon = $('<img src="' + icon + '"/>');
					break;
					
				case "function":
					icon = icon();
					break;
			}
			
			icon.
				attr('custom_map_object_number', i).
				css({top: obj.top + imgOffset.top + 'px', left: obj.left + imgOffset.left + 'px', position: 'absolute', 'z-index': 998}).
				addClass('custom_map_object').
				jeegoocontext('custom_map_object_popup', {
					modifier_disable: "ctrlKey",

					tolerate_mouseup: function(event, context, refTime, delay) {
						var now=new Date;
						return $(event.target).closest('.parameter').size() || (_mousedown.target && event.target!=_mousedown.target);
					},

					onShow: function(event, context) {
						var menu = this;
						var i = $(event.target).attr('custom_map_object_number');
						var obj = custom_map_objects[i];
						
						menu.empty();
						switch (typeof(obj.onclick)) {
							case "function":
								obj.onclick();
								break;
								
							// $() works for strings with HTML code as well as for DOM objects
							case "string":
							case "object":
								$('<li></li>').append($(obj.onclick)).appendTo(menu);
								break;
							
							default:
								console.log("Wrong custom map object onclick type");
						}

						// set 1sec timeout to hide device_menu on mouseout occuring at least 300ms after mousein,
						// and cancel timeout when mousein occurs again before menu is hidden
						menu._mouseover=false;
						menu._hideTimeout=null;
						$(document).unbind('mousemove.custom_map_object_popup').bind('mousemove.custom_map_object_popup',function(event){
							if ($(document.elementFromPoint(event.pageX,event.pageY)).closest('#custom_map_object_popup').size()) {
								if (!menu._mouseover) {
									try {
										clearTimeout(menu._hideTimeout);
									} catch(e) {};
									menu._mouseover=true;
									menu._mouseoverTime=new Date;
								}

							} else {
								if (menu._mouseover && (event.pageX||event.pageY)) {
									var now=new Date;
									if (now.getTime() - menu._mouseoverTime.getTime() > 300) {
										menu._hideTimeout=setTimeout(function(){
											$(document).jeegoocontext('reset');
											menu.empty(); // remove smart content from the menu (like video)
											$(menu).unbind('mousemove.custom_map_object_popup');
											menu._hideTimeout=null;
										},1000);
									}
									menu._mouseover=false;
								}
							}
						});
					},
					
					onHide: function() {
						menu.empty(); // remove smart content from the menu (like video)
						return true; // continue normal operation
					}					
				}).
				bind('click', device_click).
				appendTo($('#areas'));
		} catch(err) {
			console.log("Error creating custom map object" + err);
		}
	}
};

function custom_map_objects_hide() {
	$('#areas .custom_map_object').remove();
};

// execute hide/show functions for tab switching
function select_tab(tabId) {
	if (tabId == "tab_devicestatus")
		showDevicesStatus();
	else
		hideDevicesStatus();
	
	if (tabId == "tab_switches")
		showSwitches();
	else
		hideSwitches();
	
	if (tabId == "tab_sensors")
		showSensors();
	else
		hideSensors();
	
	if (tabId == "tab_meters")
		showMeters();
	else
		hideMeters();
	
	if (tabId == "tab_thermostats")
		showThermostats();
	else
		hideThermostats();
	
	if (tabId == "tab_locks")
		showLocks();
	else
		hideLocks();
	
	if (tabId == "tab_networkmanagement")
		showNetworkManagement();
	else
		hideNetworkManagement();
	
	if (tabId == "tab_routing_table")
		showRoutingTable();
	else
		hideRoutingTable();

	if (tabId == "tab_controller_info")
		showControllerInfo();
	else
		hideControllerInfo();
	
	if (tabId == "tab_raw_devices_interface") {
		$('#tab_raw_devices_interface td.leftpanel').append($('#devices_icons_list'));
		$('#devices_icons_list').css({height: tab_current().height()});
		showRawDevicesInterface();
	} else
		hideRawDevicesInterface();
	
	if (tabId == "tab_deviceconfiguration") {
		$('#tab_deviceconfiguration td.leftpanel').append($('#devices_icons_list'));
		$('#devices_icons_list').css({height: tab_current().height()});
		showConfigUI();
		deviceIconListMenu_setup();
	} else
		hideConfigUI();

	if (tabId == "tab_areas") {
		$('#areas_leftpanel').prepend($('#area_list'));
		if ($('#orphanDevice_list:visible').size())
			$('#area_list').css({height: '50%'});
		devices_parse(config.devices,rootArea);
		areaDevices_addIcons2gui();
		custom_map_objects_show();
	} else {
		$('#orphanDevice_list').empty();
		$('#areas .device_icon').remove();
		$('#area_list .area_device_list.device_context').hide();
		$('#area_list .device_icon').remove();
		custom_map_objects_hide();
	};
};

// TODO:
// setup device icon list menu
function deviceIconListMenu_setup() {
	// attach menu to rename to all device_icon except for unregistered and broadcast icons
	$('#devices_icons_list .device_icon, #orphanDevice_list .device_icon, #area_list .device_icon').filter('[device!=0]').filter('[device!=255]').jeegoocontext('deviceIconList_menu',{
		modifier_disable: 'ctrlKey',

		onShow: function(event,context) {
			contextMenu=true;
			$('.cache').css({display: "block", "z-Index": 998, opacity: cache_opacity}).bind('mousedown',function(){
				$(document).jeegoocontext('reset');
				contextMenu=false;
				$(this).hide().unbind('mousedown');
				$(context).removeClass('hover');
			});

			if ($(context).closest('#area_list').size()) {
				$('#deviceIconList_menu #device_remove').show();
			} else {
				$('#deviceIconList_menu #device_remove').hide();
			}
				
		},

		onHide: function(event,context) {
			contextMenu=false;
			$('.cache').hide().unbind('mousedown');
			$(context).removeClass('hover');
		},

		onSelect: function(event,context) {
			contextMenu=false;
			$('.cache').hide().unbind('mousedown');
			$(context).removeClass('hover');

			if ($(this).hasClass('submenu') || $(this).hasClass('disabled'))
				return;

			switch($(this).attr('id')) {
				case 'device_rename':
					device_rename(context);
					break;
				case 'device_typechange':
					device_typechange(context);
					break;
				case 'device_remove':
					device_removeFromMap($(context).attr('device'));
					break;
			}
		}
	});
};

// update device icons position / hide and show on boundaries
function devices_updatePosition() {
	var areas=$('#areas');
	var imgOffset=$('img.map').offset();

	areas.find('.device_icon').each(function() {
		var icon=$(this)
		var devnum=icon.attr('device');
		var device=$(config.devices).find('[rootarea='+$(rootArea).attr('id')+'][device='+devnum+']');
		var top=parseInt(device.attr('top'))+imgOffset.top;
		var left=parseInt(device.attr('left'))+imgOffset.left;

		deviceIcon_clip(areas,icon,left,top);

	});
};

// update the device list in select, orphan list and device lists
function deviceList_update() {
	orphanDeviceList_update();
	deviceIconListMenu_setup();
	deviceIconNames_update();
	devices_parse(config.devices,rootArea);
};

// init vertical separator

function verticalSeparator_init(callback) {

	$('td.separator').bind('mousedown',function(){

		var hoffset=11;
		var leftpanel=$('td.leftpanel');
		var rightpanel=$('td.rightpanel');
		var thisleftpanel=$(this).prev();

		var separator=this;
		$(separator).addClass('hover');
		$(separator).data('moving',true);


		$(document).unbind('.separator').bind('mousemove.separator',function(event){

			var parentWidth=$(separator).closest('table').parent().width();
			var width=event.pageX-thisleftpanel.offset().left-8;

			if (width<16 && !leftpanel.data('wait')) {

				leftpanel.data('wait',{width: leftpanel.css('width'), pageX: event.pageX});
				leftpanel.css({display: "none", width: 0});
				rightpanel_resize();

				$.cookie('verticalSeparator',"[0,"+parseInt(leftpanel.css('width'))+","+event.pageX+"]");

				if (callback) {
					callback(event);
				}

			} else { 
				var data=leftpanel.data('wait');

				if (width>parentWidth-32)
					return false;

				if (data) {
					if (event.pageX<data.pageX+32)
						return false;

					width=parseInt(data.width)+32;
					leftpanel.data('wait',null);
				};

				leftpanel.children().width(width);
				leftpanel.css({display: "block", width: width});
				rightpanel_resize();

				$.cookie('verticalSeparator',"["+width+"]");
			};

			if (callback) {
				callback(event);
			}
			return false;

		}).bind('mouseup.separator',function(event){
			$(this).unbind('.separator');
			$(separator).data('moving',false);
			if (!pointInElement(separator,event.pageX,event.pageY)) {
				$(separator).removeClass('hover');
			}
			return false;
		});

		return false;

	}).bind('mouseover',function(){
		var separator=this;
		$(separator).addClass('hover');

	}).bind('mouseout',function(){
		var separator=this;
		if (!$(separator).data('moving'))
			$(separator).removeClass('hover');
	});

};

// resize rightpanel
var nomore=false; // nomore is not used yet
function rightpanel_resize() {
	try {
		$('div.rightpanel').width($('body').width()-$('td.rightpanel:visible').offset().left-parseInt($('.tab:visible').css('padding-right')));
	} catch(e) {};

	nomore=inrow_resize($('div.attribute,div.subtag:visible',$('div.tab:visible')),'select',64);
	nomore=inrow_resize($('#event_filter_div > div.filter',$('div.tab:visible')),'select',64);
	//nomore|=inrow_resize($('.description'),'input, .intl');

};

// resize selects to fit into container
function inrow_resize(container_list,selector,minwidth) {
	var nomore=false;
	container_list.each(function(){
		var container=$(this);
		var container_rightend=container.offset().left+container.width();
		var elem_list=container.find(selector+':visible');
		var elem_list_count = elem_list.size();
		if (elem_list_count) {
			var maxwidth=(container.width()-($(elem_list[0]).offset().left-container.offset().left))/elem_list_count;
			if (maxwidth<minwidth) {
				nomore=true;
			} else {
				var avail=0;
				elem_list.each(function(){
					elem=$(this);
					var width=elem.width();
					if (width<maxwidth) {
						avail+=maxwidth-width;
					}
				});
				elem_list.css('max-width',maxwidth+((elem_list_count > 1)?avail/(elem_list_count-1):0));
			}
		}
	});
	return nomore;
};


// restore saved vertical separator position

function verticalSeparator_restore() {

	var hoffset=17;
	var defaultLeftpanelWidth=128;
	var leftpanel=$('td.leftpanel');
	var rightpanel=$('td.rightpanel');
	var parentWidth=$(leftpanel).closest('table').parent().width();

	if ($.cookie('verticalSeparator')) {

		var cookie=eval($.cookie('verticalSeparator'));

		var width=cookie[0];
		if (width) {
			leftpanel.children().width(width);
			leftpanel.css({width: width});
			rightpanel_resize();
		} else {
			leftpanel.css({display: "none"});
			leftpanel.data('wait',{width: cookie[1], pageX: cookie[2]});
		}
	} else {
		leftpanel.children().width(defaultLeftpanelWidth);
		leftpanel.css({width: defaultLeftpanelWidth});
		rightpanel_resize();
	}
};

// toggle geek mode
function setGeekMode(geek_mode) {
	$.cookie('geek_mode', geek_mode ? '1' : '0');
	$('.geek_mode').button('option','label',$.translate(geek_mode ? 'toggle_geek_mode_off' : 'toggle_geek_mode_on'));
	$.globalcss('.geek',{display: (geek_mode?'':'none')});
};

function toggleGeekMode() {
	setGeekMode($.cookie('geek_mode') != '1');
};

// show devices in area tree
function setShowDevicesInTree(showHide) {
	$.cookie('show_devices_in_tree', showHide ? '1' : '0');
	$('#area_list .area_device_list.device_context .device_icon').remove();
	$('#area_list .area_device_list.device_context').show();
	areaDevices_addIcons2gui();
	$('button.show_devices_in_tree').button('option','label',$.translate(showHide ? 'hide_devices_in_tree' : 'show_devices_in_tree'));
};

function toggleShowDevicesInTree() {
	setShowDevicesInTree($.cookie('show_devices_in_tree') != '1');
};

// get current tab
function tab_current() {
	return $('div.tab:visible');
};

// Update left and right panels height on window resize event (elements to resize differs for webkit and firefox)
function panelHeight_update() {
	tab_current().height(document.documentElement.clientHeight-55);
	var selector=($.browser.webkit?'.leftpanel, .rightpanel, div.tab td.leftpanel, div.tab td.rightpanel':'div.tab td.leftpanel, div.tab td.rightpanel')+', #devices_icons_list';
	$(selector).css({height: tab_current().height()});
	$(window).unbind('resize.panelHeight').bind('resize.panelHeight',function(){
		$(selector).css({height: tab_current().height()});
	});
};

// init toobar
function toolbar_init() {
	$('div.toolbar button.insert, div.toolbar button.append, div.toolbar button.add, div.toolbar button.duplicate').bind('click',function(){
		var context;
		var what;

		if ($(this).hasClass('insert')) {
			what='insert';
		} else if ($(this).hasClass('append')) {
			what='append';
		} else if ($(this).hasClass('add')) {
			what='add';
		} else if ($(this).hasClass('duplicate')) {
			what='duplicate';
		};
		var insert=(what=='insert');

		var tag=parent_attr(this,'tag');
		switch(tag) {
			case 'area':
				if (selectedArea.length && what!='add') {
					context=$('#area_list').find('span[area_id="'+selectedArea[((insert)?0:selectedArea.length-1)]+'"]');
				} else if (selectedArea.length && what=='add') {
					context=$('#area_list').find('span[area_id="'+selectedArea[selectedArea.length-1]+'"]');
				} else {
					context=$('#area_list [tag="area"]:'+((insert)?'first':'last'));
				}
				area_new(context[0],((insert)?'before':(($(this).hasClass('add'))?'child':'after')));
				break;

			default:
				context=$('#'+tag+'s .ui-selected:'+((insert)?'first':'last'));
				if (context.size() && what!='add') {
					tag_new(context[0],what);
				} else {
					tag_new(null,what,tag);
				}
				break;
		}
	});

	$('div.toolbar button.move').bind('click', function() {
		var tag=parent_attr(this,'tag');
		if (tag != 'scene' && tag != 'climate')
			return; // move is only possible for scenes and climate
		context=$('#'+tag+'s .ui-selected:first');
		if (!context.size())
			return;

		$('#move_dialog')[0].context=context[0];
		$('#move_dialog').attr('value',$(context[0].node).attr('area'));
		$('#move_dialog #move_name').html((tag == 'scene') ? scene_name($(context[0].node).attr('id')) : climate_name($(context[0].node).attr('id')));
		$('#move_dialog #move_description').html($.translate('move_' + tag + '_description'));

		$('#move_dialog').dialog({
			modal: true,
		
			buttons: {
				cancel : function() {
					$(this).dialog('close');
				},

				move : (tag == 'scene') ? scene_domove : climate_domove
			},

			width: 'auto',	

			open: function() {
				dialog_init(this,16/9);
				$(this).find('.area_select').attr('value', $(this).attr('value'));
				areas_htmlSelect($(this).find('.area_select'));
				$('#move_dialog select').bind('keydown',function(event) {
					if (event.keyCode==13) {
						if (tag == 'scene')
							scene_domove();
						else
							climate_domove();
					};
				});
			},

			close: function(){
				$('#move_dialog select').unbind('keydown');
			}
		});
	});

	$('div.toolbar button.delete').bind('click',toolbar_deleteButton);

	$('#area_toolbar button.rename').bind('click',function(){
		var context=$('#area_list .ui-selected:first');
		area_rename(context[0]);

	});

	$('#area_toolbar button.upload').bind('click',function(){
		var context=$('#area_list .ui-selected:first');
		area_img(context[0].area);

	});

	$('#area_toolbar button.edit').bind('click',function(){
		var context=$('#area_list .ui-selected:first');
		area_edit(context[0]);

	});

	$('#area_toolbar button.devices').bind('click',function(){
		deviceList_toggle();
		
	});

	$('#deviceconfiguration_toolbar button.rename').bind('click',function(){
		var context=$('#devices_icons_list .ui-selected:first');
		device_rename(context[0]);

	});

	$('#deviceconfiguration_toolbar button.typechange').bind('click',function(){
		var context=$('#devices_icons_list .ui-selected:first');
		device_typechange(context[0]);

	});

	$('#area_toolbar button.edit_plugins').bind('click',function(){
		edit_plugins_show();
	});

};

// show/hide orphan device list
function deviceList_toggle() {
	if ($('#orphanDevice_list:visible').size()) {
		$('#orphanDevice_list').hide();
		$('#area_list').css({height: '100%'});
		$('#area_toolbar button.devices').button('option', 'label', $.translate('devices_on_area'));
	} else {
		$('#orphanDevice_list').show().droppable({
			drop: device_dropOut
		});

		$('img.map').droppable({
			drop: device_dropOnMap
		});
		orphanDeviceList_update();
		$('#area_list').css({height: '50%'});
		$('#orphanDevice_list').css({height: '48%'});
		$('#area_toolbar button.devices').button('option', 'label', $.translate('devices_on_area_hide'));
	}
};

// update orphan device list
function orphanDeviceList_update() {

	$('#orphanDevice_list').empty().append(generateDeviceIconList());
	orphanDeviceList_filter();

	$('#orphanDevice_list .device_icon').addClass('pointer').draggable({
		containment: false,
		delay: 100,
		zIndex: 997,
		helper: 'clone',
		revert: 'invalid'

	}).css({cursor: 'move'});

	deviceIconListMenu_setup();
};

// filter orphan device list
function orphanDeviceList_filter() {
	var rootAreaId=$(rootArea).attr('id');
	$('#orphanDevice_list').find('.device_icon').show();
	$(config.devices).find('device').each(function(){
		var device=$(this);
		// hide devices placed in current rootArea
		if ((device.attr('rootarea')==rootAreaId)
		// hide devices placed in other branches
		|| (device.attr('area') && !area_sameBranch(selectedArea[0],device.attr('area')))) {
			$('#orphanDevice_list').find('.device_icon[device='+device.attr('device')+']').hide();
		}
	});
};

// display device icon on map
function deviceIcon_add2gui(div_areas,icon,top,left) {

	icon.appendTo(div_areas).css({zIndex: 997, position: "absolute", top: top, left: left}).jeegoocontext('device_menu',{

		modifier_disable: "ctrlKey",

		tolerate_mouseup: function(event,context,refTime,delay) {
			var now=new Date;
			return $(event.target).closest('.parameter').size() || (_mousedown.target && event.target!=_mousedown.target);
		},

		onShow: function(event,context) {
			var menu=this;
			createDeviceContextControls($(context).attr('device'), menu);

			// set 1sec timeout to hide device_menu on mouseout occuring at least 300ms after mousein,
			// and cancel timeout when mousein occurs again before menu is hidden
			menu._mouseover=false;
			menu._hideTimeout=null;
			$(document).unbind('mousemove.device_menu').bind('mousemove.device_menu',function(event){
				if ($(document.elementFromPoint(event.pageX,event.pageY)).closest('#device_menu').size()) {
					if (!menu._mouseover) {
						try {
							clearTimeout(menu._hideTimeout);
						} catch(e) {};
						menu._mouseover=true;
						menu._mouseoverTime=new Date;
					}

				} else {
					if (menu._mouseover && (event.pageX||event.pageY)) {
						var now=new Date;
						if (now.getTime() - menu._mouseoverTime.getTime() > 300) {
							menu._hideTimeout=setTimeout(function(){
								$(document).jeegoocontext('reset');
								$(menu).unbind('mousemove.device_menu');
								menu._hideTimeout=null;
							},1000);
						}
						menu._mouseover=false;
					}
				}
			});

		}

	}).draggable({
		start: function() {
			return $('#orphanDevice_list:visible').size()>0;
		},
		zIndex: 997,
		revert: 'invalid'

	}).bind('click',device_click);
};

// create new config.devices device
function device_new(attr) {

	attr=$.extend({
		device: null,
		description: null,
		rootarea: null,
		area: null,
		top: null,
		left: null,
		devicetype: null
	},attr);

	var device=$(document.createElement('device'));

	device.attr('device',attr.device);
	device.attr('description',attr.description);

	if (attr.rootarea) 
		device.attr('rootarea',attr.rootarea);
	if (attr.area)
		device.attr('area',attr.area);
	if (attr.top)
		device.attr('top',attr.top);
	if (attr.left)
		device.attr('left',attr.left);
	if (attr.devicetype)
		device.attr('devicetype',attr.devicetype);
	try {
		$(config.devices).append(device);
	} catch(e) {
		alert_dialog($.translate('device_new') + ': '+e);
		return null;
	}

	return device;
};

// drop device on map
function device_dropOnMap(event,ui) {

	var div_areas=$('#areas');
	var move=(ui.draggable[0].style.position=="absolute");

	var top=Math.floor(ui.position.top-$('img.map').offset().top);
	var left=Math.floor(ui.position.left-$('img.map').offset().left);

	var rootArea_id=$(rootArea).attr('id');
	var device_id=ui.draggable.attr('device');

	if (!move) {

		if ($(config.devices).find('[rootarea='+rootArea_id+'][device='+device_id+']').size())
			return;

		var icon=device_icon(device_id, false);
		deviceIcon_add2gui(div_areas,icon,ui.position.top,ui.position.left);

		// check if a free config.device node with no coordinates exists
		var freeconfig=$(config.devices)
			.find('[device='+device_id+']')
			.filter(function(){
				return !$(this).attr('rootarea');
			});

		if (!freeconfig.size()) {
			var device=device_new({
				device: icon.attr('device'),
				description: device_name(icon.attr('device'),{nameOnly: true, withoutId: true}),
				rootarea: rootArea_id,
				area: selectedArea[0],
				top: top,
				left: left
			});
	
			try {
				$(config.devices).append(device);

			} catch(e) {
				icon.remove();
				alert_dialog($.translate('device_dropOnMap') + ': '+e);
			};

		} else {
			freeconfig.attr('rootarea',rootArea_id)
				.attr('area',selectedArea[0])
				.attr('top',top)
				.attr('left',left);
		};

		$('#orphanDevice_list').find('[device="'+device_id+'"]').remove();
		
	} else {
		var device=$(config.devices).find('[rootarea='+rootArea_id+'][device='+device_id+']')
			.attr('area',selectedArea[0])
			.attr('top',top)
			.attr('left',left);
	};

	// change device area at every level in the branch and remove device from other branches
	$(config.devices)
		.find('[device='+device_id+']')
		.each(function(){
			var device=$(this);
			if (device.attr('area')) {
				if (area_sameBranch(selectedArea[0],device.attr('area')))
					device.attr('area',selectedArea[0]);
				else 
					device_removePosition(device);
			}
		});

	areas_update();
	$('#area_toolbar .save').button('enable');
};

// update map and area_list after changing config.areas or config.devices
function areas_update() {
	areas_parse(config,areasparse_root);
	$('#area_list')[0].style.display="block";
	$('#areas')[0].style.display="block";
	areamenu_setup();
	enable_hilighting();
	$('img.map').droppable({
		drop: device_dropOnMap
	});
	devices_parse(config.devices,rootArea);
	selectedArea_arrayRestore();

};

// remove device(s) position from config_device
function device_removePosition(device_list) {

	if (!device_list.size())
		return;

	// remove only attributes (to keep description)
	device_list
		.removeAttr('rootarea')
		.removeAttr('area')
		.removeAttr('top')
		.removeAttr('left');

	// remove duplicates
	device_list.each(function(){
		var id=$(this).attr('device');
		var last_with;
		var first_without;
		$(config.devices).find('[device='+id+']').filter(function(){
			if (!$(this).attr('rootarea')) {
				if (first_without==undefined)
					first_without=this;
				else 
					$(this).remove();
			} else {
				last_with=this;
				return false
			}
			
		});

		if (last_with && first_without) {
			$(first_without).remove();
		}
	});
};

function device_dropOut(event,ui) {
	var device=ui.draggable.attr('device');
	device_removeFromMap(device);
};

// remove device from map and from area list
function device_removeFromMap(device) {
	device_removePosition($(config.devices).find('[rootarea='+$(rootArea).attr('id')+'][device='+device+']'));
	$('#area_toolbar .save').button('enable');
	devices_parse(config.devices,rootArea);
	areas_update();
};

// delete selection in current tab
function toolbar_deleteButton() {

	var tag=parent_attr(this,'tag');
	var descr;
	var selection;

	if (tag=='area') {
		selection=$('#area_list span.ui-selected');
		if (selection.size()==1) {
			descr=$.translate('area')+' "'+area_name(selection.attr('area_id'))+'"';
		} else {
			descr=selection.size()+' '+$.translate('areas');
		};
		var doit;
		selection.each(function(){
			doit=area_canDelete(this.area);
			return doit;
		});
		if (doit) 
			confirm_dialog($.translate('delete')+' '+descr+' ?',$.translate('confirm'),function(){
				selection.each(function(){
					area_dodelete(this);
			});
		});
		return;
	};

	selection=$('#'+tag+'s').find('.tag.ui-selected');

	if (selection.size()==0)
		return;

	if (selection.size()==1) {
		descr=$.translate(tag)+' "'+selection.find('.tagname').html()+'"';
	} else {
		descr=selection.size()+' '+$.translate(tag+'s');
	};

	if (tag=='scene') {
		var doit;
		selection.each(function(){
			doit=scene_canDelete(this);
			return doit;
		});
		if (!doit) 
			return;
	};

	if (tag=='climate') {
		var doit;
		selection.each(function(){
			doit=climate_canDelete(this);
			return doit;
		});
		if (!doit) 
			return;
	};

	confirm_dialog($.translate('delete')+' '+descr+' ?',$.translate('confirm'),function(){
		selection.each(function(){
			tag_dodelete(this);
		});
	});
};

// hilight area
function area_hilight(area) {
	$('#area_list span[area_id="'+$(area).attr('id')+'"]').addClass('current');
	$(area.htmlArea).mouseover();
};

// setup menu for areas
function areamenu_setup() {

	$('.area_context').jeegoocontext('area_menu',{

		modifier_disable: "ctrlKey",

		onShow: function(event,context) {

			contextMenu=true;
			$('.cache').css({display: "block", "z-Index": 998, opacity: cache_opacity}).bind('mousedown',function(){
				$(document).jeegoocontext('reset');
				contextMenu=false;
				$(this).hide().unbind('mousedown');
				$(context).removeClass('hover');
			});

			if (!$(context.area.span).hasClass('ui-selected')) {
				$(context.area.span).click();
			};

			$('#area_menu > li').addClass('disabled');

			var area=$(context.area).attr('id');

			if ($(context).closest('#tab_areas').size()) {
				$('#area_menu').find('#area_new_child, #area_rename, #area_img').removeClass('disabled');
				if (context.area.parentNode.tagName.toLowerCase()=='area') {
					$('#area_menu').find('#area_new_before, #area_new_after, #area_delete').removeClass('disabled');
				};

				// disable edit when no img
				if (rootArea.img) {
					$('#area_menu > #area_edit').removeClass('disabled');
				};
			};

			if (area_zoomable(context.area) && $(context.area).attr('id')!=1) {
				$('#area_menu #area_zoom').removeClass('disabled').show().prev().show();
			} else {
				$('#area_menu #area_zoom').hide().prev().hide();
			};

			if (selectedArea_has($(rootArea).attr('id')) || !$(config.areas).find('area[id='+selectedArea[0]+'][img]').size()) {
				$('#area_menu #area_img')
					.attr('what','upload')
					.attr('title',$.translate('upload_image_area_tooltip'))
					.tooltip()
					.text($.translate('upload_image_area'));
			} else {
				$('#area_menu #area_img')
					.attr('what','remove')
					.attr('title',$.translate('remove_image_area_tooltip'))
					.tooltip()
					.text($.translate('remove_image_area'));
			}
		},

		onHide: function(event,context) {

			contextMenu=false;
			$('.cache').hide().unbind('mousedown');
			$(context).removeClass('hover');

		},

		onSelect: function(event,context) {

			contextMenu=false;
			$('.cache').hide().unbind('mousedown');
			$(context).removeClass('hover');

			if ($(this).hasClass('submenu') || $(this).hasClass('disabled'))
				return;

			switch($(this).attr('id')) {
				case 'area_zoom':
					area_zoom(context, {shiftKey: false, ctrlKey: false});
					break;
				
				case 'area_new_before':
					area_new(context,'before');
					break;

				case 'area_new_child':
					area_new(context,'child');
					break;

				case 'area_new_after':
					area_new(context,'after');
					break;

				case 'area_edit':
					area_edit(context);
					break;

				case 'area_rename':
					area_rename(context);
					break;

				case 'area_img':
					area_img(hilitArea||context.area);
					break;

				case 'area_delete':
					toolbar_deleteButton.call(context);
					break;

				case 'separator':
					break;

				default:
					console.log('Menu item not bound. ('+$(this).attr('id')+') %o %o',this,context);
					break;
			}
		}	
	});
};

// save whole config or section
function config_save(xml) {

	var tag;

	if ($(this).hasClass('save')) {
		tag=parent_attr(this,'tag');
		switch(tag) {
			case 'area':
				config_save(config.devices);
				xml=config.areas;
				break;
			case 'deviceconfiguration':
				config_conf_save(tag);
				return;
			default:
				error_msg('Unknown toolbar ('+tag+')',this);
				xml=null;
				break;
		}
	};

	if (!xml) {
		config0=null;
		config0=config.cloneNode(true);

	} else {

		var section=xml.tagName.toLowerCase();
		switch(section) {
			case 'config':
				config0=null;
				config0=xml.cloneNode(true);
				break;
			default:
				try {
					var section0=$(config0).find(section);
					if (section0.size())
						config0.removeChild(section0[0]);

				} catch(e) {
					alert_dialog($.translate('config_save') + ': '+e);
				};

				config0.appendChild(xml.cloneNode(true));
				break;
		}
	};
				
	config_dosave(config0,tag,'Rules');
};

function config_conf_save(tag) {
	config_conf0=null;
	config_conf0=config_conf.cloneNode(true);

	config_dosave(config_conf0,tag, 'Configuration');
};

// send xml to server
function config_dosave(xml,tag,file) {
	$.ajax({
		type: 'PUT',
		url: '/config/' + file + '.xml',
		dataType: 'script',
		data: $(xml).dom2text(),
		async: false,
		error: function(XMLHttpRequest, textStatus, errorThrown){
			alert_dialog($.translate('cannot_write')+' ' + file + '.xml: ' + errorThrown + ((errorThrown && XMLHttpRequest.responseText) ? ', ' : '')  + XMLHttpRequest.responseText);
		},
		success: function() {
			if (tag) {
				$('#'+tag+'_toolbar .save').button('disable');
			}
		}
	});
};

function edit_plugins_show() {
	var handler = function(code_content) {
		$('#edit_plugins_dialog #code_content').val(code_content);
		$('#edit_plugins_dialog #code_content').width(window.innerWidth*0.8);
		$('#edit_plugins_dialog #code_content').height(window.innerHeight*0.8);
		$('#edit_plugins_dialog').dialog({
			modal: true,
			
			title: $.translate('edit_plugins'),
		
			buttons: {
				cancel : function() {
					$(this).dialog('close');
				},

				save : function() {
					$.ajax({
						type: 'PUT',
						url: '/config/plugins.js',
						data: $('#edit_plugins_dialog #code_content').val(),
						async: false,
						error: function(XMLHttpRequest, textStatus, errorThrown){
							alert_dialog($.translate('cannot_write')+' plugins.js: ' + errorThrown + ((errorThrown && XMLHttpRequest.responseText) ? ', ' : '')  + XMLHttpRequest.responseText);
							$('#edit_plugins_dialog').dialog('close');
						},
						success: function() {
							$('#edit_plugins_dialog').dialog('close');
						}
					});	
				}
			},

			width: 'auto'
		});
	};
	
	$.ajax({
		type: 'GET',
		url: '/config/plugins.js',
		async: false,
		success: handler,
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			if (XMLHttpRequest.status != 200)
				handler(''); // go with empty file
			else {
				handler(XMLHttpRequest.responseText);
				alert_dialog($.translate('cannot_read')+' plugins.js: ' + errorThrown + ((errorThrown && XMLHttpRequest.responseText) ? ', ' : '')  + XMLHttpRequest.responseText);
			}
		}
	});
};

// get new area id
function area_newId() {
	var id=1;
	while (true) {
		if (!$(config.areas).find('area[id="'+id+'"]').size()) {
			if (!$(config).find('[area="'+id+'"]').size())
				return id;
		};
		++id;
	}
};

// check if point is in element
function pointInElement(elem,x,y) {
	elem=$(elem);
	var rect=elem.offset();
	rect.right=rect.left+elem.width();
	rect.bottom=rect.top+elem.height();
	//console.log("%d.%d, %d.%d, %d.%d",rect.left,rect.top,rect.right,rect.bottom,x,y);
	return x>=rect.left && x<=rect.right && y>=rect.top && y<=rect.bottom;
};

// check if rect corners are in element
function rectInElement(elem,left,top,width,height) {
	return pointInElement(elem,left,top) && pointInElement(elem,left+width,top) && pointInElement(elem,left+width,top+height) && pointInElement(elem,left,top+height);
};

// to be more cross-borwser we may use in future this solution:
// http://www.backalleycoder.com/2010/10/05/converting-css-named-colors-to-rgb-and-hex/
function colourName2Hex(colour) {
	var d = document.createElement("div");
	d.style.color = colour;
	document.body.appendChild(d);
	var rgbForm = window.getComputedStyle(d, '').color;
	if (!rgbForm) {
		console.log('Use cross-broser version of ');
		return '#000000';
	};
	var rgbArr = rgbForm.match(/rgb\(([0-9]+), *([0-9]+), *([0-9]+)\)/);
	if (!rgbArr)
		return '#000000';
	return ('#000000' + (parseInt(rgbArr[1], 10)*256*256 + parseInt(rgbArr[2], 10)*256 + parseInt(rgbArr[3], 10)).toString(16)).slice(7);
};

// enable area hilighting
function enable_hilighting() {
	$('area[coords]').each(function() {
		var areaId = $(this).attr('id');
		// add same for climate schedules // TODO !!
		if (!ZWaveAPIData.areas || !ZWaveAPIData.areas.data[areaId] || !ZWaveAPIData.areas.data[areaId].scene) // probably zones are not saved yet, so backend does not know this area Id
			return;
		var sceneId = ZWaveAPIData.areas.data[areaId].scene.value;
		var colour = sceneId ? $(config.scenes).find('scene[id=' + sceneId + '] description').attr('colour') : "";
		$(this).data('maphilight', (colour && colour !== "") ? {
			fillColor: colourName2Hex(colour),
			fillOpacity: "0.3",
			strokeColor: colourName2Hex(colour),
			strokeOpacity: "0.5",
			alwaysOn: true,
			fade: $.browser.mozilla?true:false
		} : {});
	});
	$('img.map').maphilight({
		fillColor: "0000ff",
		fillOpacity: "0.1",
		fade: $.browser.mozilla?true:false,
		mouseoverDisable: function() {
			return false;
		},
		mouseoutDisable: function(){
			return contextMenu;
		}
	});

	$('span.area_list').each(function(){

		// hilite area_list span and region
		$(this).unbind('.hilighting').bind('mouseover.hilighting',function(event) {

			if (hilitArea && ($(this.area).attr('id')==$(hilitArea).attr('id'))) {
				return false;
			}

			link_dohilight(this.area,0);
			$(this.area.htmlArea).mouseover();

		}).bind('mouseout.hilighting', function(event) {

			if (hilitArea && pointInElement(hilitArea.span,event.pageX,event.pageY))
				return false;

			link_dohilight(this.area,1);
			$(this.area.htmlArea).mouseout();

		}).bind('click.hilighting',function(event) {
			try {
				event.preventDefault();
			} catch(e) {};

		});

	});

	$('area').unbind('.hilighting').bind('mouseover.hilighting',function(){link_dohilight(this.area,0)});
	$('area').bind('mouseout.hilighting',function(){link_dohilight(this.area,1)});
	$('#area_list').unbind('.hilighting').bind('mouseover.hilighting',function(){
		link_dohilight(hilitArea,0)
	});
	$('td.separator').unbind('.hilighting').bind('mouseover.hilighting',function(){
		link_dohilight(hilitArea,1)
	});
};

// rename area dialog
function area_rename(context) {

	var area=$(context).attr('id')?$(context).attr('id'):$(context).attr('area_id');
	$('#rename_dialog')[0].context=context;
	$('#rename_dialog #original_name').html('');
	$('#rename_dialog input').attr('value',area_name(area));

	$('#rename_dialog').dialog({
		modal: true,
	
		buttons: {
			cancel : function() {
				$(this).dialog('close');
			},

			area_rename : area_dorename
		},

		width: 'auto',	

		open: function() {
			dialog_init(this,16/9);
			$('#rename_dialog input')[0].select();
			$('#rename_dialog input').bind('keydown',function(event) {
				if (event.keyCode==13) {
					area_dorename()
				};
			});
		},

		close: function(){
			$('#rename_dialog input').unbind('keydown');
		}
	});
};

// rename device dialog
function device_rename(context) {

	var device=$(context).attr('device');
	$('#rename_dialog')[0].context=context;
	$('#rename_dialog #original_name').html(device_name(device,{nameOnly: false, withoutId: false}));
	$('#rename_dialog input').attr('value',device_name(device,{nameOnly: true, withoutId: true}));

	$('#rename_dialog').dialog({
		modal: true,
	
		buttons: {
			cancel : function() {
				$(this).dialog('close');
			},

			device_rename : device_dorename
		},

		width: 'auto',	

		open: function() {
			dialog_init(this,16/9);
			$('#rename_dialog input')[0].select();
			$('#rename_dialog input').bind('keydown',function(event) {
				if (event.keyCode==13) {
					device_dorename()
				};
			});
		},

		close: function(){
			$('#rename_dialog input').unbind('keydown');
		}
	});
};

// rename device
function device_dorename() {

	var $div=$('#rename_dialog');
	var context=$div[0].context;
	var device=$(context).attr('device');
	var name=$div.find('input').val();

	var duplicate=$(config.devices).find('[description="'+name+'"]');
	if (duplicate.size()) {
		if (duplicate.filter('[device='+device+']').size()) {
			// no change
			$div.dialog('close');
			return;
		} else {
			alert_dialog($.translate('duplicate_name')+': '+name, 'Warning'); // just notify and do rename
		};
	};

	if (!$(config.devices).find('device[device='+device+']').attr('description',name).size()) {
		device_new({
			device: device,
			description: name
		});
	};

	deviceIconNames_update();
	devices_htmlSelect_update();
	$('#area_toolbar .save').button('enable');

	$div.dialog('close');
};

// type change device dialog
function device_typechange(context) {

	var device=$(context).attr('device');
	$('#typechange_dialog')[0].context=context;
	$('#typechange_dialog #original_type').html(device_type(device));

	$('#typechange_dialog').dialog({
		modal: true,
	
		buttons: {
			cancel : function() {
				$(this).dialog('close');
			},

			device_typechange : device_dotypechange
		},

		width: 'auto',	

		open: function() {
			dialog_init(this,16/9);
			$('#typechange_dialog #new_type').empty();
			if (device_types_list) {
				$('#typechange_dialog #new_type').append('<option value="">' + $.translate('no_custom_type') + '</option>');
				for (var i in device_types_list)
					$('#typechange_dialog #new_type').append('<option value="' + device_types_list[i] + '">' + device_types_list[i] + '</option>');
			}
		},

		close: function(){
			$('#typechange_dialog input').unbind('keydown');
		}
	});
};

// type change device
function device_dotypechange() {

	var $div=$('#typechange_dialog');
	var context=$div[0].context;
	var device=$(context).attr('device');
	var devicetype=$div.find('#new_type').val();

	var duplicate=$(config.devices).find('[devicetype="'+devicetype+'"][device='+device+']');
	if (duplicate.size()) {
		// no change
		$div.dialog('close');
		return;
	};

	if (!$(config.devices).find('device[device='+device+']').attr('devicetype',devicetype).size()) {
		device_new({
			device: device,
			devicetype: devicetype
		});
	};

	deviceIcons_update();
	devices_htmlSelect_update();
	$('#area_toolbar .save').button('enable');

	$div.dialog('close');
};

// translate dialog buttons
function dialog_buttons_translate() {
	$('.ui-dialog-buttonpane .ui-button-text').html(function(index,html){
		return $.translate(html)
	});
};

// rename area
function area_dorename() {
	var $div=$('#rename_dialog');
	var context=$div[0].context;
	var area=$(context).attr('id')?$(context).attr('id'):$(context).attr('area_id');
	var name=$div.find('input').attr('value');

	var duplicate=$(config.areas).find('[name="'+name.replace(/'/,"''")+'"]'); // " //just for joe editor syntax hilight to work
	if (duplicate.size()) {
		if (duplicate.filter('[id='+area+']').size()) {
			// no change
			$div.dialog('close');
			return;
		} else {
			alert_dialog($.translate('duplicate_name')+': '+name, 'Warning'); // just notify and do rename
		};
	};

	$(config.areas).find('area[id='+area+']').attr('name',name);
	$('#area_list').find('a[area_id='+area+']').html(name);
	$('#area_toolbar .save').button('enable');
	$div.dialog('close');
};

// area image upload dialog
function area_img(context) {

	switch($('#area_toolbar .upload').attr('what')) {
		case 'remove':
			confirm_dialog($.translate('confirm_zoomed_area_device_position_removal'),$.translate('confirm'),function(){
				var subtree=$(config).find('areas > area[id='+$(context).attr('id')+']');
				area_recursiveDelete(subtree[0],null);
				subtree.remove();
				make_children(config.areas);
				areaToolbar_update();
			});
			break;

		case 'upload':
			$('#upload_dialog')[0].context=context;

			$('#upload_dialog').dialog({
				modal: true,

				buttons: {
					cancel : function() {
						$(this).dialog('close');
					},

					img_upload : area_img_upload
				},

				width: 'auto',

				open: function() {
					dialog_init(this);
				},

				close: function(){
					$('#upload_dialog').unbind('keydown');
				}
			});
			break;
	}

};

// area image upload
function area_img_upload () {
	var $div=$('#upload_dialog');
	var context=$div[0].context;
	var area=$(context).attr('id')?$(context).attr('id'):$(context).attr('area_id');
	var filename_arr=$('#upload_dialog input.file').val().split('.');
	var filename_ext=(filename_arr.length>1) ? filename_arr[filename_arr.length-1] : '';
	var filename=area + '.' + filename_ext;
	var img_src='/config/maps/' + filename;
	$('form#upload_img').attr('action', img_src + '?save=yes');

	$('form#upload_img').ajaxSubmit({

		forceSync: true,

		iframe: true,

		dataType: 'json',

		success: function() {

			if (img_src) { 

				var context=$div[0].context;

				var zoom=(context.parentNode.tagName.toLowerCase()!='areas');
				if (zoom) {

					var existing=$(config).find('areas > area[id='+$(context).attr('id')+']');
					if (existing.size()) {
						// change image
						existing.attr('img',img_src);

					} else {
						// new image
						
						var clone=$(context)
							.clone(true)
							.attr('img',img_src);

						// remove coordinates from self and child areas
						clone
							.removeAttr('coords')
							.find('area').each(function(){
								$(this).removeAttr('coords');
							});
	
						$(config.areas).append(clone);
						make_children(config.areas);
					}
					area_zoom(context,{shiftKey: false, ctrlKey: false});

				} else {
					$(context).attr('img',img_src);
					areas_update();
				};

				$('#area_toolbar .save').button('enable');

			} else {
				alert_dialog($.translate('upload_failed'));
			}
		}
	});

	$div.dialog('close');
};

// Upload backup of config and restore it
function restore_backup() {
	$('#restore_backup_dialog').find('#restore_chip_info').attr('checked', false);
	$('#restore_backup_dialog').dialog({
		modal: true,

		buttons: {
			cancel : function() {
				$(this).dialog('close');
			},

			restore_backup_upload : function() {
				alert_dialog($.translate('restore_wait'), $.translate('restore_backup_upload'));
				
				$('form#restore_backup').attr('action', '/ZWaveAPI/Restore?restore_chip_info=' + ($('#restore_backup_dialog').find('#restore_chip_info').attr('checked') ? 1 : 0));
				$('form#restore_backup').ajaxSubmit({
					
					forceSync: true,
					
					iframe: true,
					
					success: function(result) {
						if (result && result.replace(/(<([^>]+)>)/ig,"") !== "null") {
							alert_dialog($.translate('restore_backup_failed') + ': ' + result);
						} else {
							alert_dialog($.translate('restore_done_reload_ui'), $.translate('restore_backup_upload'));
						}
					}
				});

				$(this).dialog('close');
			}
		},

		width: 'auto',

		open: function() {
			dialog_init(this);
		},

		close: function(){
			$('#restore_backup_dialog').unbind('keydown');
		}
	});

};

// display alert dialog
function alert_dialog(message,title) {

	$('#alert_message').html(message);

	$('#alert_dialog').dialog({

		modal: true,

		title: $.translate(title||'Error'),

		width: 'auto',

		open: function() {
			dialog_init(this,16/9);
		},

		buttons: {
			ok : function() {
				$(this).dialog('close');
			}
		}	
	});
};

// close alert dialog from code (not by button press)
function alert_dialog_close() {
	$('#alert_dialog').dialog('destroy');
};

// display error message and object in console
function error_msg(msg,debug) {

	if (console) {
		console.log("%s: %o",msg,debug);
		if (debug.stack)
			console.log(debug.stack);
	};

	alert_dialog(msg);
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

// get config from server
function config_load() {
	$.ajax({
		type: 'GET',
		url: '/config/Rules.xml',
		dataType: 'xml',
		async: false,
		success: config_parse,
		error: function(XMLHttpRequest, textStatus, errorThrown){
			config_parse($('<root><config></config></root>')[0]);
			// go with empty file
			// alert_dialog($.translate('cannot_read')+' Rules.xml: ' + errorThrown + ((errorThrown && XMLHttpRequest.responseText) ? ', ' : '')  + XMLHttpRequest.responseText);
		}
	});

	$.ajax({
		type: 'GET',
		url: '/config/Configuration.xml',
		dataType: 'xml',
		async: false,
		success: config_conf_parse,
		error: function(XMLHttpRequest, textStatus, errorThrown){
			config_conf_parse($('<root><config></config></root>')[0]);
			// go with empty file
			//  alert_dialog($.translate('cannot_read')+' Configuration.xml: ' + errorThrown + ((errorThrown && XMLHttpRequest.responseText) ? ', ' : '')  + XMLHttpRequest.responseText);
		}
	});
};

// parse Rules.xml
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
			case 'devices':
				config.devices=section;
				break;
			default:
				error_msg($.translate('syntax_error')+' '+section.tagName,section);
				break;
		}
	};

	if (!config.coordinates) {

		var coordinates=document.createElement('coordinates');
		coordinates.setAttribute('latitude',0);
		coordinates.setAttribute('longitude',0);

		$(config).append(coordinates);
		config._children.push(coordinates);
		config.coordinates=coordinates;

		config_save(config.coordinates);
	};

	if (!config.areas) {

		var areas=document.createElement('areas');

		$(config).append(areas);
		config._children.push(areas);
		config.areas=areas;

		var area=$('<area id="1" name="'+$.translate('All')+'"></area>')[0];
		area._children=new Array();

		$(config.areas).append(area);

		config.areas._children=new Array();
		config.areas._children.push(area);

		config_save(config.areas);
	};

	if (!config.devices) {
		var devices=document.createElement('devices');
		try {
			$(config).append(devices);
		} catch(e) {
			alert_dialog('config_parse: '+e);
		}
		config.devices=devices;
	}

	var cookie=$.cookie('rootHistory');
	if (cookie) rootHistory=eval('['+cookie+']');
	areasparse_root=$.cookie('areasparse_root');

	// check areas referenced in cookies are still valid
	if (areasparse_root) {
		if (!$(config.areas).find('[id='+areasparse_root+']').size()) {
			rootHistory=new Array;
			areasparse_root=null;
		}

	};

	$(rootHistory).each(function(idx,id){
		if (!$(config.areas).find('[id='+id+']').size()) {
			rootHistory=new Array;
			areasparse_root=null;
			return false;
		}
	});

	areas_parse(config,areasparse_root);
};

// parse device config
function config_conf_parse(xml) {

	if (xml.childNodes.length!=1 || xml.childNodes[0].tagName.toLowerCase()!='config') {
		error_msg($.translate('syntax_error')+' '+xml.childNodes[0].tagName,xml.childNodes[0]);
		return;
	};

	config_conf=xml.childNodes[0];

	make_children(config_conf);

	config_conf0=config_conf.cloneNode(true);

	for (var i=0; i<config_conf._children.length; ++i) {

		var section=config_conf._children[i];

		switch(section.tagName.toLowerCase()) {
			case 'devices':
				config_conf.devices=section;
				break;
			default:
				error_msg($.translate('syntax_error')+' '+section.tagName,section);
				break;
		}
	};

	if (!config_conf.devices) {

		var devices=document.createElement('devices');

		$(config_conf).append(devices);
		config_conf._children.push(devices);
		config_conf.devices=devices;

		config_conf_save();
	};
};

// remove position for all config.devices items owned by the branch.
function area_recursiveDelete(branch,nodeId) {

	if ($(branch).attr('id')==nodeId || !nodeId) {
		var isroot=$(branch).attr('img');
		if (isroot) {
			device_removePosition($(config.devices).find('[rootarea='+$(branch).attr('id')+']'));
			$(branch).removeAttr('img');
		}
	};

	for (var i=0; i<branch._children.length; ++i) {

		var id=$(branch._children[i]).attr('id');

		if (id==nodeId || !nodeId) {

			isroot=$(branch._children[i]).attr('img');
			if (isroot) {
				device_removePosition($(config.devices).find('[rootarea='+id+']'))
				$(branch._children[i]).removeAttr('img');
			};

			if (nodeId) {
				branch._children[i].parentNode.removeChild(branch._children[i]);
				branch._children.splice(i,1);
				--i;

				var remain=$(config.areas).find('[id='+id+']');
				if (!remain.size()) {
					device_removePosition($(config.devices).find('[area='+id+']'));
				}
			};

			continue;
		};

		if (branch._children[i]._children.length) {
			area_recursiveDelete(branch._children[i],nodeId);
		}
	}
};

// select change handler
// When called for one of "select.device, select.instance, select.commandclass, select.method"
// propagates to next item of the same group with group_next()

function select_change() {

	var span=$(this).parent();
	var what=span.attr('class').split(' ')[1];

	var attributeName;
	switch(what) {
		case 'device':
			var device=parseInt($(this).getSelection());
			if (device === null) return;
			span.attr('value',device);

			if (span.hasClass('node')) {
				parent_attr(span, 'value', parameter_set(span,device));
			} else { 
				if (span.hasClass('srcnode')) {
					attributeName='srcnodeid';

				} else if (span.hasClass('dstnode')) {
					attributeName='dstnodeid';

				} else if (span.hasClass('device')) {
					attributeName='id';
				} else {
					alert_dialog('select_change device: attributeName undefined');
					console.log('span', span);
					return;
				};
				
				parentNode_setAttr(span,attributeName,device);

				var instance_span=group_next(span,'instance');
				if (instance_span && span.parent().hasClass('timedevent')) {
					var ispan = $(instance_span);
					var block=(ispan.parent().hasClass('attribute'))?ispan.parent():ispan;
					block.show(); // Show instance select box, since it is mandatory for events. It might be hidden later if only one value is available.
				};
				if (instance_span && ($(instance_span).is(':visible') || (attributeName!='srcnodeid' && attributeName!='dstnodeid'))) {
					var select=instances_htmlSelect(instance_span,device);
					select_change.call(select);
				}	
			};

			break;

		case 'parameter':
			var parameter=$(this).getSelection();
			$(this).parent().find('span.parameter').each(function(){
				if ($(this).attr('parameter')==parameter) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
			break;
	}
};

// set given parent_node(elem) attribute and enable save button when value changed
function parentNode_setAttr(elem,attributeName,value) {
	if (attributeName==undefined) {
		alert_dialog('parentNode_setAttr: attributeName undefined');
		console.trace();
		return;
	}
	var node=$(parent_node(elem));
	if (node.attr(attributeName)!=value) {
		node.attr(attributeName,value);
		save_enable(elem);
	};
};

// set input fields event bindings
function input_bindings(elem) {
	var input=elem.find('input[type="text"]:not(.no_binding)');
	if (input.size()) {
		input
			.unbind('.input_bindings')
			.bind('blur.input_bindings',input_blur)
			.bind('keyup.input_bindings',input_blur);
	};

	var input=elem.find('input[type="radio"]:not(.no_binding)');
	if (input.size()) {
		input
			.unbind('.input_bindings')
			.bind('click.input_bindings',radio_click);
	};
};

// enable save button
function save_enable(elem) {

	var node=parent_node(elem);

	if ($(elem).hasClass('nosave') || !node)
		return;

	var tagName=node.tagName.toLowerCase();

	while(node && tagName!="area" && tagName!='scene' && tagName!='climate' && tagName!='rule' && tagName!='schedule' && tagName!='deviceconfiguration') {
		node=node.parentNode;
		if (!node) break;
		tagName=node.tagName.toLowerCase();
	};

	if (!node)
		return;

	$('#'+tagName+'_toolbar .save').button('enable');
};

function bitcheck_click() {
	var cur_value = eval(parent_attr(this, 'value'))[parent_attr(this, 'parameter')];
	var bit_value = 1 << $(this).attr('bit');
	var new_value = cur_value - (cur_value & bit_value) + (this.checked ? bit_value : 0);
	parent_attr(this,'value','[' + repr_array(parameter_set(this,parseInt(new_value, 10))) + ']');
};

// radio button click handler
function radio_click(event) {
	var radio=$(this);
	var val = radio.attr('value');
	if (radio.hasClass('fix') || radio.hasClass('node') || radio.hasClass('range')) {
		val = parseInt(val);
		if (isNaN(val)) {
			// do not pring this message, but use minimal value as initial instead
			// alert_dialog('radio_click: no default value for radiobutton');
			if (radio.hasClass('range')) {
				slider = $('.slider[slider=' + radio.attr('slider') + ']')
				val=parseInt(slider.attr('min'), 10);
				if (isNaN(val)) {
					alert_dialog('radio_click: no minimal value defined for slider');
					return;
				}
				slider.slider('value', val);
			}
		}

	} else {
		if (val==undefined) {
			alert_dialog('radio_click: no default value for radiobutton');
			return;
		}
	};

	parent_attr(this.parentNode, 'value', '[' + repr_array(parameter_set(this,val)) + ']');
};

// input blur handler
function input_blur() {

	var input=this;
	var $input=$(input);
	var span=$input.closest('.value');
	var what=span.attr('class').split(' ')[1];
	if (what==undefined) {
		alert_dialog('input_blur: what is undefined');
		return;
	};

	switch(what) {
		case 'bitrangePos':
			var slider=$input.prev('[slider='+$input.attr('slider')+']');
			var numValue = parseInt(input.value, 10);
			if (isNaN(numValue))
				numValue = 0;
			if (input.value !== numValue.toString(10))
				input.value = numValue.toString(10);
			if (parseInt(slider.attr('value'), 10) != parseInt(input.value, 10)) {
				var cur_value = eval(parent_attr(this, 'value'))[parent_attr(this, 'parameter')];
				var bitrange_value = ((1<<($(input).attr('bit_to')-$(input).attr('bit_from')+1))-1) << $(input).attr('bit_from');
				var new_value = cur_value - (cur_value & bitrange_value) + (this.checked ? bitrange_value : 0);
				parent_attr(this,'value','[' + repr_array(parameter_set(this,parseInt(new_value, 10))) + ']');
				slider.attr('value',parseInt(new_value, 10));
			};
			break;
		case 'sliderPos':
			var slider=$input.prev('[slider='+$input.attr('slider')+']');
			var numValue = parseInt(input.value, 10);
			if (isNaN(numValue))
				numValue = 0;
			if (input.value !== numValue.toString(10))
				input.value = numValue.toString(10);
			if (parseInt(slider.attr('value'), 10) != parseInt(input.value, 10)) {
				parent_attr('value','[' + repr_array(parameter_set(input,parseInt(input.value, 10) + parseInt(slider.attr('shift') || 0, 10))) + ']');
				slider.attr('value',parseInt(input.value, 10));
			};
			$(input.parentNode).find('input.range[slider=' + $input.attr('slider') + ']').attr('checked',true);
			break;

		case 'string':
			if (parent_attr(input,'value')==input.value)
				break;
			parent_attr(input,'value',parameter_set(input,input.value));
			// continue with default case

		default:
			var node=$(parent_node(input));

			if (node.attr(what)!=input.value) {
				node.attr(what,input.value);
				save_enable(input);
			};

			break;
	}

};

// textarea blur handler
function textarea_blur() {

	var node=$(parent_node(this));

	if (node.text()==this.value)
		return;

	node.text(this.value);
	save_enable(this);
};

// update selection menu
function htmlSelect_update(get_htmlSelect) {

	var span=this.parentNode;
//      var ref=$(this).getSelection();
	
	$(this).remove();
	
	var select=get_htmlSelect(span);
	
	// problem?: child select or parameter span could have new values (eg: after adding methodclasses to span.value.commandclass)
	// but calling select_change for nothing will enable save button
//      if (ref!=select.getSelection()) {
		select_change.call(select);
//      }

};

// update device select menu
// propagates to next item of the same group through htmlSelect_update() -> select_change() -> group_next() 
function devices_htmlSelect_update() {

	devices_htmlSelect_cache=null;
	devices_htmlSelect_cache=new Array;

	$('select.device').each(function(){
		if ($(this).closest('.condcheck').size() && $(this).closest('.condcheck').css('display') == 'none')
			return; // skip hidden condcheck src_node_id and dst_node_id
		htmlSelect_update.call(this,devices_htmlSelect);
	});
};

// update instance select menu
// Propagates to next item of the same group through htmlSelect_update() -> select_change() -> group_next() 
// Parameters are optional
function instances_htmlSelect_update(device) {

	if (device!=undefined) {
		instances_htmlSelect_cache[device]=null;
		$('.value.instance[device="'+device+'"]').find('select').each(function(){
			htmlSelect_update.call(this,function(span){
				return instances_htmlSelect(span,device);
			});
		});

	} else {
		instances_htmlSelect_cache=null;
		instances_htmlSelect_cache=new Array;
		$('select.instance').each(function(){
			htmlSelect_update.call(this,function(span){
				return instances_htmlSelect(span,span.getAttribute('device'));
			});
		});
	};
};

// update commandclass select menu
// propagates to next item of the same group through htmlSelect_update() -> select_change() -> group_next() 
// Parameters are optional
function commandClasses_htmlSelect_update(device,instance) {

	var spanList;

	if (device!=undefined) {

		if (instance!=undefined) {
			if (!commandClasses_htmlSelect_cache[device])
				commandClasses_htmlSelect_cache[device]=new Array;
			commandClasses_htmlSelect_cache[device][instance]=null;
			spanList=$('.value.commandclass[device="'+device+'"][instance="'+instance+'"]');
		} else {
			commandClasses_htmlSelect_cache[device]=null;
			spanList=$('.value.commandclass[device="'+device+'"]');
		}

	} else {
		commandClasses_htmlSelect_cache=null;
		commandClasses_htmlSelect_cache=new Array;
		spanList=$('.value.commandclass');
	};

	spanList.find('select').each(function(){
		htmlSelect_update.call(this,function(span){
			return commandClasses_htmlSelect($(span).closest('span.value.commandclass'),span.getAttribute('device'),span.getAttribute('instance'));
		});
	});
};

// check if device has no matching methodclass
function device_without_methodclass(dev,methodclass_list) {

	if (!methodclass_list)
		methodclass_list=defaultMethodClass;

	if (!(dev in ZWaveAPIData.devices)) {
		alert_dialog($.translate('device_does_not_exist') + ': ' + dev);
		return true; // device no longer exists
	};

	for (var i in ZWaveAPIData.devices[dev].instances) {

		if (!ZWaveAPIData.devices[dev].instances[i].commandClasses)
			continue;

		for (var cc in ZWaveAPIData.devices[dev].instances[i].commandClasses) {

			var methodFound = false;
			$(methodclass_list).each(function(idx,methodclass) {

				if (ZWaveAPIData.devices[dev].instances[i].commandClasses[cc][methodclass]) {

					for (var method in ZWaveAPIData.devices[dev].instances[i].commandClasses[cc][methodclass]) {

						for (var param in ZWaveAPIData.devices[dev].instances[i].commandClasses[cc][methodclass][method]) {
							methodFound = true;
							return false; // break the loop
						};
					};
				};
			});
			if (methodFound)
				break;
		};
		if (methodFound)
			break;
	};
	return !methodFound;
};

// check if commandclass has no matching methodclass
function commandClass_without_methodclass(dev,instance,commandClass,methodclass_list) {
	if (!(dev in ZWaveAPIData.devices)) {
		alert_dialog($.translate('device_does_not_exist') + ': ' + dev);
		return true; // device no longer exists
	};
	if  (!(instance in ZWaveAPIData.devices[dev].instances) || !(commandClass in ZWaveAPIData.devices[dev].instances[instance].commandClasses)) {
		alert_dialog($.translate('instance_commandclass_does_not_exist') + ': ' + dev + '#' + instance + '.' + commandClass);
		return true; // instance or command class no longer exists
	};

	$(methodclass_list||defaultMethodClass).each(function(idx,methodclass) {

		if (ZWaveAPIData.devices[dev].instances[instance].commandClasses[commandClass][methodclass]) {

			for (var method in ZWaveAPIData.devices[dev].instances[instance].commandClasses[commandClass][methodclass]) {

				for (var param in ZWaveAPIData.devices[dev].instances[instance].commandClasses[commandClass][methodclass][method]) {
					return false;
				};
			};
		};
	});
	return true;
};

// device filter for device select menu
function devices_htmlSelect_filter(span,dev,type) {
	// return true means to skip this node
	switch(type) {
		case 'srcnode':
			// allow everything, since events can come from any device via timed_event
			return false;

			// skip virtual, controller or broadcast as event source
			//return ( (ZWaveAPIData.devices[dev].data.isVirtual.value || dev == ZWaveAPIData.controller.data.nodeId.value || dev == 255));

		case 'dstnode':
			// skip not virtual, not controller and not broadcast as event destination
			return (!(ZWaveAPIData.devices[dev].data.isVirtual.value || dev == ZWaveAPIData.controller.data.nodeId.value || dev == 255));

		case 'device':
			return ZWaveAPIData.devices[dev].data.isVirtual.value || dev == ZWaveAPIData.controller.data.nodeId.value;

		case 'node':
			// skip non-FLiRS sleeping in list of associations/wakeup node notifications/... in CC params of type node
			return (!ZWaveAPIData.devices[dev].data.isListening.value && !ZWaveAPIData.devices[dev].data.sensor250.value && !ZWaveAPIData.devices[dev].data.sensor1000.value);

		default:
			return false;
	}
};

// get type of device field
function valueSpan_devType(span) {
	/*
	if (span.hasClass('srcnode') || span.hasClass('srcnodeid_filter')) {
		return 'srcnode';
	} else if (span.hasClass('dstnode') || span.hasClass('dstnodeid_filter')) {
		return 'dstnode';
	} else 
	*/
	if (span.hasClass('node')) {
		return 'node';
	} else {
		return 'device';
	}
};

var devices_htmlSelect_cache=new Array;
// build device select menu
function devices_htmlSelect(span) {

	span=$(span);
	var selected=span.attr('value');
	var dontAddInvalid=span.hasClass('dontaddinvalid');
	var type=valueSpan_devType(span);

	if (!devices_htmlSelect_cache[type]) {

		var options='';

		try {
			for (var dev in ZWaveAPIData.devices) {
				if (devices_htmlSelect_filter(span,dev,type)) {
					continue;
				};
				options+='<option value="'+dev+'">'+device_name(dev)+'</option>';
			}

		} catch(e) {
			error_msg(e);
		};

		devices_htmlSelect_cache[type]=$('<select class="device">'+options+'</select>').css(htmlSelectStyle);
	};

	var select=devices_htmlSelect_cache[type].clone();
	
	// show in simple mode only child devices of area.
	var sceneId = parseInt($(span).closest('.tag.scene').attr('scene'), 10);
	if (!isNaN(sceneId) && $.cookie('geek_mode') != '1')
		select.find('option').filter(function() {
			var devId = $(this).attr('value');
			return !(device_area(devId) == scene_area(sceneId) || parseInt(devId) == parseInt(selected));
		}).remove();

	// if no device is selected, select the first visible in the list and update span and node
	if (isNaN(parseInt(selected)) && span.is(':visible')) {
		selected = select.find('option').first().attr('value');
		span.attr('value', selected);
		var attributeName = null;
		switch (type) {
			case 'srcnode':
				attributeName = 'srcnodeid';
				break;
			case 'dstnode':
				attributeName = 'dstnodeid';
				break;
			case 'device':
				attributeName = 'id';
				break;
			default:
				break;// do not do it for 'node' type
		}
		if (attributeName)
			$(parent_node(span)).attr(attributeName, selected);
	};

	if (!select.find('[value="'+selected+'"]').size()) {
		if (dontAddInvalid) 
			selected=null;
		else 
			select.html('<option value="'+selected+'" class="undefined">'+device_name(selected)+'</option>'+select.html());
	};

	select.select(selected);

	if (select.find('option').size()<2) {
		select.disabled=true;
	};

	span.html('');
	span.append(select);
	select.bind('change',select_change);
	return select;
};

var instances_htmlSelect_cache=new Array;
// build instance select menu
function instances_htmlSelect(span,device) {

	span=$(span);
	span.attr('device',device);

	var selected=span.attr('value');
	var dontAddInvalid=span.hasClass('dontaddinvalid');

	device=parseInt(device);

	if (!instances_htmlSelect_cache[device]) {

		var count=0;
		if (ZWaveAPIData && ZWaveAPIData.devices && ZWaveAPIData.devices[device]) {
			var options='';

			try {
				for (var instance in ZWaveAPIData.devices[device].instances) {
					++count;
					options+='<option value="'+instance+'">'+instance_name(device,instance)+'</option>';
				}

			} catch(e) {
				error_msg(e);
			};

			instances_htmlSelect_cache[device]=$('<select'+(count<=1?' disabled':'')+'>'+options+'</select>').css(htmlSelectStyle);

			if (!count)
				instances_htmlSelect_cache[device].css({width: "128px"});

		} else {
			instances_htmlSelect_cache[device]=$('<select disabled></select>').css({width: "128px","font-family": "monospace"});
		}
	};

	var select=instances_htmlSelect_cache[device].clone();	

	if (!select.find('[value="'+selected+'"]').size()) {
		if (dontAddInvalid) 
			selected=null;
		else 
			select.html('<option value="'+selected+'">'+instance_name(device,selected)+'</option>'+select.html());
	};

	select.select(selected);
	
	if (!span.parent().hasClass('condcheck')) {

		var block=(span.parent().hasClass('attribute'))?span.parent():span;

		if (select.find('option').size()>1)
			block.show();
		else
			block.hide();
	};

	span.html('');
	span.append(select);
	select.bind('change',select_change);

	return select;
};

var commandClasses_htmlSelect_cache=new Array;
var commands_htmlSelect_cache=new Array;
var methods_htmlSelect_cache=new Array;
// build commandclass select menu
function commandClasses_htmlSelect(span,device,instance) {

	span=$(span);
	span.attr('device',device);
	span.attr('instance',instance);
	var methodclass_list=span.attr('methodclass_list')?span.attr('methodclass_list').split(' '):defaultMethodClass;

	var selected=span.attr('value');
	var dontAddInvalid=span.hasClass('dontaddinvalid');

	if (!commandClasses_htmlSelect_cache[device]) {
		commandClasses_htmlSelect_cache[device]=new Array;
	}

	var select;

	if (!commandClasses_htmlSelect_cache[device][instance] || (methodclass_list.length>1 || methodclass_list[0]=='userSet')) {

		var count=0;
		try {
			// build device instance's commandClasses <select>
			var options='';
			var commandclasses_done=new Array;

			$(methodclass_list).each(function(i,methodclass) {

				for (var commandclass in ZWaveAPIData.devices[device].instances[instance].commandClasses) { // if the device or instance or command class does not longer exist we handle the exception

					if (commandclasses_done[commandclass]) {
						continue;
					};

					if (!methods_htmlSelect(null,device,instance,commandclass,new Array(methodclass)).size())
						continue;

					commandclasses_done[commandclass]=true;

					// build device instance's commandClasses select <option>
					options+='<option value="'+commandclass+'">'+commandClass_name(device,instance,commandclass)+'</option>';

					++count;
				};

				select=$('<select'+(count<=1?' disabled':'')+'>'+options+'</select>').css(htmlSelectStyle);

			});

			if (!count)
				select.css({width: "128px"});

		} catch (e) {
			select=$('<select disabled></select>').css({width: "128px","font-family": "monospace"});
		};

		if (methodclass_list.length==1 && methodclass_list[0]=='userSet')
			commandClasses_htmlSelect_cache[device][instance]=select;
	};

	if (methodclass_list.length==1 && methodclass_list[0]=='userSet')
		select=commandClasses_htmlSelect_cache[device][instance].clone();

        if (!(device in ZWaveAPIData.devices)) {
                alert_dialog($.translate('device_does_not_exist') + ': ' + device);
                return; // device no longer exists
        };
        if  (!(instance in ZWaveAPIData.devices[device].instances)) {
                alert_dialog($.translate('instance_commandclass_does_not_exist') + ': ' + device + '#' + instance);
                return; // instance no longer exists
        };

	if (!ZWaveAPIData.devices[device].instances[instance].commandClasses[selected])
		selected = null;

	if (selected !== null && !select.find('[value="'+selected+'"]').size()) {
		if (dontAddInvalid) 
			selected=null;
		else 
			select.html('<option value="'+selected+'">'+commandClass_name(device,instance,selected)+'</option>'+select.html());
	};

	select.select(selected);

	if (select.find('option').size()>1)
		span.show().attr('disabled', '');
	else if (select.find('option').size() == 1)
		span.show().attr('disabled', 'disabled');
	else
		span.hide();
		
	span.html('');
	span.append(select);
	select.bind('change',select_change);

	return select;
		
};

// build commandclass menu
function commandClass_menu(commandClass,methodclass_list) {

	var options='';

	$(methodclass_list||defaultMethodClass).each(function(i,methodclass) {
		if (commandClass[methodclass]) {
			for (var method in commandClass[methodclass]) {
				var _method=$.translate(method);
				options+='<option value="'+method+'" methodclass="'+methodclass+'">'+_method+'</option>';
			}
		}
	});

	var ret=new Object();
	ret.options=options;

	return ret;
};

// build method gui
function method_rawGui(method,settings) {

	settings=$.extend({
		immediate: false
	},settings);

	var labels = new Array();
	var html='';
	var count=method.length;
	var immediate=0;

	method.forEach(function(val,parameter){
		labels.push($.translate(val.label));
		var param=$('<span class="parameter" parameter="'+parameter+'">'+parameter_gui('', val, true)+'</span>');
		if (settings.immediate && !settings.immediatekeepbutton && count == 1) {
			param.find('input[type="radio"]').each(function(){
				if ($(this).parent().next().hasClass('slider')) {
					$(this).parent().hide().next().next().hide();
				} else {
					var fix=$(this).hasClass('fix')?' fix':'';
					var value=$(this).attr('value')==undefined?'':' value="'+$(this).attr('value')+'"';
					var button=$('<button class="radio'+fix+'"'+value+' radio="r'+radiobutton_id+'">'+$(this).parent().text()+'</button>');
					$(this).parent().replaceWith(button);
				}
			});
		}
		html+='<span class="parameter" parameter="'+parameter+'">'+param.html()+'</span>';
	});

	return {labels: labels, count: count, html: html};

};

// build parameter gui
function parameter_gui(id, parameter, first) {

	var html='';

	for (var property in parameter.type) {
		var label = id == '' ? '' : $.translate(parameter.label);

		switch(property) {
			case 'fix':
				if (id == '')
					error_msg('Parameter type fix is not allowed at top level: should be inside a enum of: ' + property)
				else
					html+='<label>' + (id == '' ? '' : ('<input class="fix" type="radio" name="'+id+'" value="'+parameter.type[property].value+'"/>')) + label + '</label>';
				break;

			case 'range':
				++slider_id;
				html+='<label>' + (id == '' ? '' : ('<input class="range" type="radio" name="'+id+'" slider="'+slider_id+'"/>')) + label + '</label><span class="slider" min="'+parameter.type[property].min+'" max="'+parameter.type[property].max+'" ' + (parameter.type[property].shift ? ('shift="'+parameter.type[property].shift + '"') : '') + ' slider="'+slider_id+'"></span><input type="text" class="value sliderPos" value="" slider="'+slider_id+'"/>';
				break;

			case 'bitrange': // not supposed to be inside a radio
				++slider_id;
				// &nbsp; is a hack for Chrome to render the slider correctly
				html+='<br/>&nbsp;<span class="slider bitrange" bit_from="'+parameter.type[property].bit_from+'" bit_to="'+parameter.type[property].bit_to+'" min="0" max="'+((1 << (parameter.type[property].bit_to-parameter.type[property].bit_from+1))-1)+'" slider="'+slider_id+'"></span><input type="text" class="value bitrangePos" value="" slider="'+slider_id+'"/>';
				break;

			case 'bitcheck': // not supposed to be inside a radio
				html+='<br/><input class="bitcheck" type="checkbox" bit="'+parameter.type[property].bit+'" value="" />';
				break;

			case 'node':
				html+='<label>' + (id == '' ? '' : ('<input class="node" type="radio" name="'+id+'"/>')) + label + '<span class="value device node" value=""></span></label>';
				break;

			case 'string':
				html+='<label>' + (id == '' ? '' : ('<input class="string" type="radio" name="'+id+'"/>')) + label + '<span class="value string"><input class="string" type="text"/></span></label>';
				break;

			case 'climate_schedule':
				html+='<label>' + (id == '' ? '' : ('<input class="climate_schedule" type="radio" name="'+id+'"/>')) + label + '<span class="value climate_schedule"><img class="climate_schedule" src="pics/icons/schedule.png"></span></label>';
				break;

			case 'enumof':
				if (first)
					html+='<span class="enumof">';
				else
					html+='<span class="enumof indent">';

				if (id == '') {
					id = 'r'+radiobutton_id;
					++radiobutton_id;
				} else
					html+='<label class="enumof">' + label + '</label>';

				for (var index in parameter.type[property])
					html+=parameter_gui(id+'_',parameter.type[property][index], false);

				html+='</span>';
				break;

			default:
				error_msg('Property unhandled: '+property,parameter.type); 
				break;
		}

		break; // parameter has only one "type" value from the list fix, range, node, string, climate_schedule, enumof, bitrange, bitcheck
	};

	return html;
};

/*
// #if admin
// build methods select menu
function methods_htmlSelect(span,device,instance,commandclass,methodclass_list) {

	methodclass_list=methodclass_list||defaultMethodClass;

	if (span) {
		span=$(span);
		span.attr('device',device);
		span.attr('instance',instance);
		span.attr('commandclass',commandclass);
		span.attr('methodclass_list', methodclass_list.join(' '));
		var selected=span.attr('value');
		var dontAddInvalid=span.hasClass('dontaddinvalid');
		var select;
	};

	if (!methods_htmlSelect_cache[device]) {
		methods_htmlSelect_cache[device]=new Array();
	};

	if (!methods_htmlSelect_cache[device][instance]) {
		methods_htmlSelect_cache[device][instance]=new Array();
	};

	if (!methods_htmlSelect_cache[device][instance][commandclass]) {
		methods_htmlSelect_cache[device][instance][commandclass]=new Array();
	};

	var options=$();
	$(methodclass_list).each(function(idx,methodclass) {

		if (!(device in ZWaveAPIData.devices)) {
			alert_dialog($.translate('device_does_not_exist') + ': ' + device);
			return true; // device no longer exists
		};
		if  (!(instance in ZWaveAPIData.devices[device].instances) || !(commandclass in ZWaveAPIData.devices[device].instances[instance].commandClasses)) {
			alert_dialog($.translate('instance_commandclass_does_not_exist') + ': ' + device + '#' + instance + '.' + commandclass);
			return true; // instance or command class no longer exists
		};

		if (!methods_htmlSelect_cache[device][instance][commandclass][methodclass]) {
			var ret=commandClass_menu(ZWaveAPIData.devices[device].instances[instance].commandClasses[commandclass], new Array(methodclass));
			if (!ret.options.length)
				return true;

			var methods_select=$('<select>'+ret.options+'</select>').css(htmlSelectStyle);
			methods_htmlSelect_cache[device][instance][commandclass][methodclass]=methods_select;

			var ul=$('#cc_'+device+'_'+instance+'_'+commandclass+'_'+methodclass);
			if (ul.size())
				ul.html(ret.menu);
			else
				$(document.body).append($('<ul id="cc_'+device+'_'+instance+'_'+commandclass+'_'+methodclass+'" style="display: none" class="jeegoocontext cm_default">'+ret.menu+'</ul>'));

		}
		options=options.add(methods_htmlSelect_cache[device][instance][commandclass][methodclass].find('option'));
	});

	if (!span)
		return options;

	select=$('<select>').append(options.clone()).css(htmlSelectStyle);

        if (!(device in ZWaveAPIData.devices)) {
                alert_dialog($.translate('device_does_not_exist') + ': ' + device);
                return; // device no longer exists
        };
        if  (!(instance in ZWaveAPIData.devices[device].instances) || !(commandclass in ZWaveAPIData.devices[device].instances[instance].commandClasses)) {
                alert_dialog($.translate('instance_commandclass_does_not_exist') + ': ' + device + '#' + instance + '.' + commandclass);
                return; // instance or command class no longer exists
        };

	// check if currently selected method should be added to the list
	var selected_methodclass;
	if (!ZWaveAPIData.devices[device].instances[instance].commandClasses[commandclass])
		selected = null;
	else {
		// do not add the method if it does not exist for this CC (check all method classes!)
		var found = false;
		for (mc in allMethodClasses) {
			if (ZWaveAPIData.devices[device].instances[instance].commandClasses[commandclass][allMethodClasses[mc]] && ZWaveAPIData.devices[device].instances[instance].commandClasses[commandclass][allMethodClasses[mc]][selected]) {
				found = true;
				selected_methodclass = allMethodClasses[mc];
			}
		}
		if (!found)
			selected = null;
	}

	if (selected !== null && !select.find('[value="'+selected+'"]').size()) {
		if (dontAddInvalid) 
			selected=null;
		else 
			select.html('<option value="'+selected+'" methodclass="'+selected_methodclass+'">'+selected+'</option>'+select.html());
			//select.html('<option value="'+selected+'" class="undefined">'+selected+' ('+$.translate('undefined')+')</option>'+select.html());
	};
	select.select(selected);
	span.attr('value',select.getSelection());

	if (select.find('option').size()>1)
		span.show().attr('disabled', '');
	else if (select.find('option').size() == 1)
		span.show().attr('disabled', 'disabled');
	else
		span.hide();

	span.html('');
	span.append(select);

	select.bind('change',select_change);

	return select;
};

var scenes_htmlSelect_root="-1";
var scenes_htmlSelect_cache=new Array;

// build scenes select menu
function scenes_htmlSelect(span,all) {

	span=$(span);
	var selected=span.attr('value');
	var dontAddInvalid=span.hasClass('dontaddinvalid');
	var areaSpecific=span.hasClass('areaSpecific');
	var addUndefined=span.closest('.condarea').size();
	if (areaSpecific) {
		var refArea=span.prevAll('.value.area:first').attr('value');
	}

	// skip scene if in activatescene inside this scene
	var skip;
	var context=span.closest('.tag');
	if (context.attr('tag')=='scene') {
		skip=context[0].node.getAttribute('id');
	};


	if (!areasparse_root)
		all=1;

	var index=all ? 1 : 0; // all may be true/false, while index should be 1/0

	if (!scenes_htmlSelect_cache[index] || (!all && (scenes_htmlSelect_root != areasparse_root))) {

		var options=new Array();
		$(config.scenes).find('scene').each(function(idx){

			var area=$(this).attr('area');

			if (all||area_isDescendant(areasparse_root,area)) {
				var rec=new Object;
				rec.i=idx;
				rec.area=area;
				xmlarea=$(config.areas).find('area[id='+area+']');
				if (xmlarea.size()) {
					rec.seq=xmlarea[0].seq;
					options.push(rec);
				}
			}
		});

		// sort scenes by area display order
		options.sort(function(a,b){
			if (a.seq>b.seq)
				 return 1;
			else if (a.seq<b.seq)
				 return -1;
			else
				 return 0;
		});

		var html='';
		var area=-1;
		var i;
		for (i=0; i<options.length; ++i) {

			var scene=$(config.scenes._children[options[i].i]).attr('id');

			if (area!=options[i].area) {
				if (area!=-1) {
					html+='</optgroup>';
				};
				area=options[i].area;
				html+='<optgroup label="'+area_name(area)+'" area="'+area+'" >';
			};

			html+='<option value="'+scene+'">'+scene_name(scene)+'</option>';

		};
		if (i) {
			html+='</optgroup>';
		};

		scenes_htmlSelect_cache[index]=$('<select class="scenes">'+html+'</select>').css(htmlSelectStyle);
	};

	var select=scenes_htmlSelect_cache[index].clone();

	if (areaSpecific) {
		select.html(select.find('optgroup[area="'+refArea+'"]').html());
	}

	var config_update=false;

	if (skip) {
		select.find('option[value="'+skip+'"]').remove();
	};

	// remove empty optgroup (areas with no scenes)
	select.find('optgroup:empty').remove();

	if (addUndefined) {
		select.prepend($('<option value="-1">'+$.translate('scene_undefined')+'</option>'));
	};

	if (selected=='') { // new element, select first option
		selected=select.find('option:first').attr('value');
		span.attr('value',selected);
		config_update=true;

	} else {
		if (!select.find('[value="'+selected+'"]').size()) {
			var scene=$(config.scenes).find('scene[id="'+selected+'"]');
			if (scene.size()) { // selected by geek ...
				select.html('<option value="'+selected+'" class="selectedByGeek">'+scene_name(selected)+'</option>'+select.html());
			} else {
				if (dontAddInvalid) 
					selected=null;
				else 
					select.html('<option value="'+selected+'" class="undefined">'+scene_name(selected)+'</option>'+select.html());
			}
		}
	};

	select.select(selected);

	span.html('');
	span.append(select);
	select.bind('change',select_change);

	if (config_update) {
		select.change();
	};

	if (!all) {
		scenes_htmlSelect_root=areasparse_root;
	}

	return select;

};
*/

// parse areas config
// return true if something has been parsed
function areas_parse(config,root) {

	areasparse_root=root;
	$.cookie('areasparse_root',areasparse_root);

	$('#areas,#area_list').hide();
	$('#areas')[0].innerHTML='';
	$('#area_list')[0].innerHTML='';

	hilitArea=null;
	rootArea=null;

	for(var i=0; i<config.areas._children.length; ++i) {

		var node=config.areas._children[i];

		if (node.tagName.toLowerCase()!="area") {
			error_msg($.translate('syntax_error')+' '+node.tagName,node);
			continue;
		};

		if (root) {
			// if parameter "root" has been specified,
			// parse matching subtree 
			if ($(node).attr('id')==root) {	
				rootArea=node;
				config.areas.seq=0;
				area_parse(node,0,node);
				areaDevices_addIcons2gui();
				return true;
			}

		} else {
			// else first node is main tree
			rootArea=node;
			config.areas.seq=0;
			$('#areas').fadeOut();
			area_parse(node,0,node);
			areaDevices_addIcons2gui();
			return true;
		}
	};

	// Add device icons in area list
	areaDevices_addIcons2gui();

	return false;
};

// parse single area config
function area_parse(area,nestingLevel,root) {

	area.seq=config.areas.seq++;

	if (!area.path) {

		if (area.parentNode.tagName.toLowerCase()!='area') {
			area.path=new Array;
		} else {
			area.path=area.parentNode.path.slice();
		};

		area.path.push($(area).attr('id'));
	}

	area_add2gui(area,nestingLevel,root);
	areaDevices_add2gui(area,nestingLevel,root);

	for (var i=0; i<area._children.length; ++i) {
		var node=area._children[i];
		if (node.tagName.toLowerCase()!="area") {
			error_msg($.translate('syntax_error')+' '+node.tagName,node);
			continue;
		};
		area_parse(node,nestingLevel+1,root);
	}
};

// check if area area_id is descendant of area root_id
function area_isDescendant(root_id,area_id) {

	var root=$(config.areas).find('area[id='+root_id+']')[0];
	var area=$(config.areas).find('area[id='+area_id+']')[0];

	if (!area.path)
		return false;

	var rootPathLen=root.path.length;
	var areaPathLen=area.path.length;

	if (areaPathLen<rootPathLen)
		return false;

	for (var i=0;i<rootPathLen;++i)
		if (root.path[i]!=area.path[i])
			return false;

	return true;
};


function area_sameBranch(id1,id2) {
	return id1 && id2 && (area_isDescendant(id1,id2) || area_isDescendant(id2,id1));
};

// display area in area list, load map if any and create html area
function area_add2gui(area,nestingLevel,root) {

	// add entry to indented area list
	var span=$('<span area_id="'+$(area).attr('id')+'" class="area_list area_context cm_default" tag="area">')[0];
	span.draggable=false;
	span.style.paddingLeft=(16+10*nestingLevel)+'px';
	span.area=area;
	span.node=area;
	span.onclick=area_click;
	span.ondblclick=area_click;

	var img=document.createElement('img');
	if (area._children.length) {
		img.className='collapse';
		img.src='pics/minus.png';
//		img.onclick=function(){arealist_collapse(area)};
	} else {
		img.className='leaf';
		img.src='pics/nothing.png';
	};
	img.draggable=false;

	var a=document.createElement('a');
	a.className='area_list';
	a.innerHTML=$(area).attr('name');
	a.draggable=false;
	a.area=area;
	a.setAttribute('area_id',$(area).attr('id'));

	$(span).append(img);
	$(span).append(a);
	$('#area_list').append(span);

	if (nestingLevel==0) {

		// load image and create empty html map
		var imgsrc=$(area).attr('img');
		if (imgsrc) {

			var img=document.createElement('img');
			$(img).bind('load',function(){
				setTimeout(function(){
					devices_parse(config.devices,rootArea);
					custom_map_objects_show();
				},300);
			});
			img.className="map";
			img.src=imgsrc;

			$('#areas').append(img);

			img.map=document.createElement('map');
			img.map.setAttribute('name',$(area).attr('id'));

			$('#areas').append(img.map);

			img.setAttribute('usemap','#'+$(area).attr('id'));
			area.img=img;

		}
	};

	// add html area
	var htmlArea;
	if ($(area).attr('coords')) {
		htmlArea=document.createElement('area');
		htmlArea.setAttribute('id',$(area).attr('id'));
		htmlArea.setAttribute('name',$(area).attr('id'));
		$(htmlArea).bind('click',area_click).bind('dblclick',area_click);
		htmlArea.setAttribute('shape','poly');
		htmlArea.setAttribute('coords',$(area).attr('coords'));
		htmlArea.className="area_context";
		htmlArea.setAttribute('tag','area');
		htmlArea.area=area;
		htmlArea.span=span;

		if (root.img)
			root.img.map.insertBefore(htmlArea,(root.img.map.childNodes.length?root.img.map.childNodes[0]:null));

	};

	area.htmlArea=htmlArea;
	area.span=span;

};

// add device icons to area list
// called from area_parse() and each time area list or device list is changed
function areaDevices_addIcons2gui() {
	$('#area_list .area_device_list.device_context').hide();
	if ($.cookie('show_devices_in_tree') == '1') {
		$('#area_list .area_device_list.device_context .device_icon').remove();
		$('#area_list .area_device_list.device_context').each(function () {
			$(this).append(device_icon($(this).attr('device'),true));
		}).show();
	}
};

// display devices in area list
function areaDevices_add2gui(area,nestingLevel,root) {

	$(config.devices).find('[rootarea='+$(root).attr('id')+'][area='+$(area).attr('id')+']').each(function(){
		var device=this;
		var span=$('<span device="'+$(device).attr('device')+'" class="area_device_list device_context cm_default" tag="device">')[0];
		span.draggable=false;
		span.style.paddingLeft=(16+10*(nestingLevel+1))+'px';
		span.onclick=device_click;
		$('#area_list').append(span);
	});
};

// check if area is zoommable
function area_zoomable(area) {

	var id=$(area).attr('id');
	
	for (var i=0; i<config.areas._children.length; ++i) {
		if ($(config.areas._children[i]).attr('id')==id) {
			return true;
		}
	};

	return false;
};

// zoom area
function area_zoom(area, event) {

	if (area.area)
		area = area.area;

	if ($(area).attr('id')==$(rootArea).attr('id')) {
		// root node, nothing to unzoom
		if (!rootHistory.length) {
			area_select(area,event);
			return;
		};

		// unzoom
		var root=rootHistory.pop();
		$.cookie('rootHistory',rootHistory.toString());

		if (!areas_parse(config,root))
			return;

		selectedArea_arrayRestore();

	} else {
		// zoom
		var root=$(rootArea).attr('id');
		var from=$(area).attr('id');

		if (!areas_parse(config,from))
			return;

		rootHistory.push(root);
		$.cookie('rootHistory',rootHistory.toString());

		selectedArea_arrayRestore();
	};

	$('#area_list, #areas').fadeIn(null,function(){
		areamenu_setup();
		deviceIconListMenu_setup();
		enable_hilighting();
		$('img.map').droppable({
			drop: device_dropOnMap
		});
	});
}

// device icon click handler
function device_click(event) {
	var areas=$('#areas');
	var clicked=areas.find('.device_icon.click').removeClass('.click');
	if (clicked.size()) {
		if ($(this).attr('device')==clicked.attr('device')) {
			event.type='contextmenu';
			clicked.trigger(event);
		}
	};
	$(this).addClass('click');

	// show menu on first click
	event.type='contextmenu';
	$(this).trigger(event);	
};

var click_timeout;
// area click handler
function area_click(event) {

	var area=this.area||this;

	if (event && event.type=='dblclick' && area_zoomable(area)) {
		if (click_timeout) clearTimeout(click_timeout);
		area_zoom(area,event);

	} else {
		if ($.browser.mobile && event && event.type!='dblclick' && selectedArea[0]==$(area).attr('id') && !contextMenu) {
			event.type='contextmenu';
			var span=this.span||this;
			if (click_timeout) clearTimeout(click_timeout);
			click_timeout=setTimeout(function(){$(span).trigger(event)},200);	
		};
	};

	area_select(area,event);
};

// handle single area and multiple scene/climate/rule/schedule selection
function list_select(event,div,elem,selection,attributeName) {

	if (event && event.shiftKey) {

		// already selected, do nothing
		if (elem.hasClass('ui-selected') && selection.length==1) {
			return;
		};

		// nothing selected yet, just set selection
		if (!selection.length) {
			elem.addClass('ui-selected');

		// extend selection
		} else {

			var first;
			var last;
			var from;
			var to;
		
			if (elem[0].area) {
				return; // no multiple selection for areas
    			} else {
				var elem_list=div.find('['+attributeName+']');
				first=elem_list.index(div.find('['+attributeName+'="'+selection[0]+'"]'));
				last=elem_list.index(div.find('['+attributeName+'="'+selection[selection.length-1]+'"]'));
				var elem_index=elem_list.index(div.find('['+attributeName+'="'+elem.attr(attributeName)+'"]'));
				if (first>elem_index) {
					from=elem_index;
					to=last;
					div.data('anchor',last);
				} else if (last<elem_index) {
					from=first;
					to=elem_index;
					div.data('anchor',first);
				} else {
					if (!div.data('anchor'))
						div.data('anchor',first);

					if (div.data('anchor')<elem_index) {
						from=div.data('anchor',attributeName);
						to=elem_index;
					} else {
						from=elem_index;
						to=div.data('anchor',attributeName);
					}
				};

				div.find('.ui-selected').removeClass('ui-selected');
				for (var seq=from; seq<=to; ++seq) {
					$(elem_list.get(seq)).addClass('ui-selected');
				};
			}
		}

 	// no shift key
	} else {

		// already selected
		if (elem.hasClass('ui-selected')) {

			// toggle selection
			if (event && event.ctrlKey)  {
				if (!elem[0].area) // no multiple selection for areas
					 elem.removeClass('ui-selected');

			// set new selection
			} else if (selection.length>1) {
				$(div).find('.ui-selected').removeClass('ui-selected');
				elem.addClass('ui-selected');

			// do nothing
			} else {
				return;
			}

		// not selected yet
		} else {

			// toggle selection
			if (event && event.ctrlKey) {
				if (!elem[0].area) // no multiple selection for areas
					elem.addClass('ui-selected');

			// set selection
			} else {
				$(div).find('.ui-selected').removeClass('ui-selected');
				elem.addClass('ui-selected');
			}
		}
	};


};

// area selection
function area_select(area,event) {

	var area_list=$('#area_list');
	var span=area_list.find('span[area_id="'+$(area).attr('id')+'"]');

	list_select(event,area_list,span,selectedArea);

	if (!$('#area_list .ui-selected').size()) {
		$('#area_list span[area_id]:first').click();
	}

	selectedArea_arrayUpdate();

// #if admin
	orphanDeviceList_filter();
// #endif admin
};

// update and save selectedArea array
function selectedArea_arrayUpdate() {

	selectedArea.splice(0,selectedArea.length);

	$('#area_list span.ui-selected').each(function(){
		selectedArea.push(parseInt(this.getAttribute('area_id')));
	});

	selectedArea_arraySave();
};

// save selectedArea array
function selectedArea_arraySave() {
	$.cookie('selectedArea',selectedArea.toString());
	areaToolbar_update();
};

// restore saved selectedArea array
function selectedArea_arrayRestore() {
	if ($.cookie('selectedArea'))
		selectedArea=eval('['+$.cookie('selectedArea')+']');

	if (selectedArea.length) {
    		$(selectedArea).each(function(i,area){
			$('#area_list span[area_id="'+area+'"]').addClass('ui-selected');
		});
	};

	if (!$('#area_list .ui-selected').size()) {
		$('#area_list span[area_id]:first').click();
	}
}

// #if admin
var selectedDevice=new Array;
// select device
function device_select(event) {

	var device_list=$('#devices_icons_list');
	var div=$(this);

	// {} is just to disable multiselect
	list_select({},device_list,div,selectedDevice,'device');

	if (!device_list.find('.ui-selected').size()) {
		device_list.find('div[device]:first').click();
	}
	selectedDevice_arrayUpdate();

	//$('#tab_devices_list .rightpanel').find('[device]').hide();
	//$('#devices_icons_list div.ui-selected').each(function(){
	//	$('#tab_devices_list .rightpanel').find('[device="'+$(this).attr('device')+'"]').show();
	//});
};

// update selectedDevice array
function selectedDevice_arrayUpdate() {

	selectedDevice.splice(0,selectedDevice.length);

	$('#devices_icons_list div.ui-selected').each(function(){
		selectedDevice.push(parseInt(this.getAttribute('device')));
	});

	selectedDevice_arraySave();
};

// save selectedDevice array
function selectedDevice_arraySave() {
	$.cookie('selectedDevice',selectedDevice.toString());
};

// restore saved selectedDevice array
function selectedDevice_arrayRestore() {
	if ($.cookie('selectedDevice'))
		selectedDevice=eval('['+$.cookie('selectedDevice')+']');

	if (selectedDevice.length)
    		$(selectedDevice).each(function(i,device) {
			$('#devices_icons_list div[device="'+device+'"]').addClass('ui-selected');
		});

	if (!$('#devices_icons_list .ui-selected').size())
		$('#devices_icons_list div[device]:first').click();
	else
		$('#devices_icons_list .ui-selected').first().click();
};

// display "nothing to display" when tag list is empty
function nothingToDisplay(tag,joking) {

	$('#'+tag+'s .nothingtodisplay').remove();
	if (joking)
		return;

	if (!$('#'+tag+'s > .'+tag+':visible').size()) {
		var div=$('<div class="nothingtodisplay"><textnode class="intl nothingtodisplay" id="no_'+tag+'_to_display"></textnode></div>').translate().prependTo('#'+tag+'s');
	}
}

// update area toolbar
function areaToolbar_update() {
	$('#area_toolbar').find('.insert, .append, .delete').button((selectedArea_has($(rootArea).attr('id')) || !selectedArea.length)?'disable':'enable');
	$('#area_toolbar .edit').button((selectedArea.length && rootArea.img)?'enable':'disable');
	if (selectedArea_has($(rootArea).attr('id')) || !$(config.areas).find('area[id='+selectedArea[0]+'][img]').size()) {
		$('#area_toolbar .upload').attr('what','upload').attr('title',$.translate('upload_image_area_tooltip')).tooltip();
		$('#area_toolbar .upload .ui-button-text').text($.translate('upload_image_area'));
	} else {
		$('#area_toolbar .upload').attr('what','remove').attr('title',$.translate('remove_image_area_tooltip')).tooltip();
		$('#area_toolbar .upload .ui-button-text').text($.translate('remove_image_area'));
	}
};

var toolbar_update=new Object;
toolbar_update['area']=areaToolbar_update;

// create new orphan area and display dialog
function area_new(context,where) {

	if ((where=='before'|| where=='after') && context.area.parentNode.tagName.toLowerCase()!='area')
		return;

	var newid=area_newId();
	var area=$('<Area id="'+newid+'" name="'+$.translate('Area')+' '+newid+'"></Area>')[0];
	area_new_dialog(area,context,where);
};

// new area dialog
function area_new_dialog(area,context,where) {

	$('#rename_dialog #original_name').html('');
	$('#rename_dialog input').attr('value',$(area).attr('name'));

	$('#rename_dialog').dialog({
		modal: true,
	
		buttons: {
			cancel : function() {
				$(this).dialog('close');
			},

			area_new : function() {
				area_donew(area,context,where);
			}
		},
	
		open: function() {
			dialog_init(this,16/9);
			$('#rename_dialog input')[0].select();
			$('#rename_dialog input').bind('keydown',function(event) {
				if (event.keyCode==13) {
					area_donew(area,context,where);
					return false;
				}	
			});
			dialog_buttons_translate();
		},

		close: function(){
			$('#rename_dialog input').unbind('keydown');
		}

	});
};

// set area path
function area_setPath(_area) {
	if (_area[0].parentNode.path) {
		_area[0].path=_area[0].parentNode.path.slice();
		_area[0].path.push($(_area[0].parentNode).attr('id'));
	}
};

// add new area to config and gui, and edit region
function area_donew(area,context,where) {

	$(area).attr('name',$('#rename_dialog input').attr('value'));
	$('#rename_dialog').dialog('close');

	var contextId=$(context.area).attr('id');

	switch(where) {
		case 'before':

			$(config.areas).find('area[id="'+contextId+'"]').each(function(){

				var _area=$(area).clone();
				_area.insertBefore(this);

				_area[0]._children=new Array();
				for (var i=0; i<_area[0].parentNode._children.length;++i) {
					if ($(_area[0].parentNode._children[i]).attr('id')==contextId) {
						_area[0].parentNode._children.splice(i,0,_area[0]);
						break;
					}
				};

				area_setPath(_area);
	
			});
			break;
			
		case 'after':

			$(config.areas).find('area[id="'+contextId+'"]').each(function(){

				var _area=$(area).clone();
				_area.insertAfter(this);

				_area[0]._children=new Array();
				
				for (var i=0; i<_area[0].parentNode._children.length;++i) {

					if ($(_area[0].parentNode._children[i]).attr('id')==contextId) {

						if (i+1==_area[0].parentNode._children.length) {
							_area[0].parentNode._children.push(_area[0]);
					
						} else {
							_area[0].parentNode._children.splice(i+1,0,_area[0]);
						};
						break;
					}
				};

				area_setPath(_area);
			});
			break;

		case 'child':
		default:
			$(config.areas).find('area[id="'+contextId+'"]').each(function(){

				var _area=$(area).clone();
				_area.appendTo(this);

				_area[0]._children=new Array();
				
				this._children.push(_area[0]);

				area_setPath(_area);
			});
			break;
	};

	$(area).remove();
	save_enable(context);

	$(context.area.htmlArea).mouseout();
	areas_parse(config,areasparse_root);
	$('#area_list')[0].style.display="block";
	$('#areas')[0].style.display="block";
	areamenu_setup();
	$('img.map').droppable({
		drop: device_dropOnMap
	});
	area_edit($('#area_list').find('span[area_id="'+$(area).attr('id')+'"]')[0]);

};

// confirmation dialog
function confirm_dialog(msg,_title,callback) {

	$('#confirm_dialog > span').html(msg);

	if (jQuery.isFunction(callback))
		$('#confirm_dialog').dialog({
			modal: true,

			title: _title,
		
			buttons: {
				cancel : function() {
					$(this).dialog('close');
				},

				ok : function() {
					$(this).dialog('close');
					callback();
				}
			},
		
			open: function() {
				dialog_init(this,16/9);
			},

			width: "auto"
		});
	else
		$('#confirm_dialog').dialog({
			modal: true,

			title: _title,
		
			buttons: callback,
		
			open: function() {
				dialog_init(this,16/9);
			},

			width: "auto"
		});
};

function area_canDelete(xmlArea) {
	return true;
};

// delete area
function area_dodelete(context) {

	var xmlArea=context.area;
	var areaId=$(xmlArea).attr('id');

	// find previous area at same or higher level
	var prev=$('#area_list').find('span.[area_id='+areaId+']').prev();
	while (prev.size()) {
		if (prev[0].area && prev[0].area.path.length<=xmlArea.path.length)
			break;
		prev=prev.prev();
	};

	area_recursiveDelete(config.areas,areaId);
	$('#area_toolbar .save').button('enable');

	prev.addClass('ui-selected');
	selectedArea_arrayUpdate();

	areas_update();
	areaToolbar_update();
};

function interface_update() {
	areas_update();
};

// edit area
function area_edit(context) {

	var coords;

	// pressed edit again: quit edit mode
	if (context.area.region) {
		var region=context.area.region;	
		if (region.mode=="edit"||region.mode=="design") {
			region.close({force: true});
			context.area.region=null;
		};
		return;
	}

	if (rootArea.img) {
		var found;
		$(document.region).each(function(i,region){
			if (region.mode=="edit"||region.mode=="design") {
				region.close({force: true});
				found=true;
				return false;
			}
		});
		if (found)
			return;
	}

	var areaId=$(context.area).attr('id');
	if (!context.area.htmlArea || $(context.area.htmlArea).attr('coords').split(',').length<6) { // edit new region
		if (rootArea.img) {

			$('.cache').css({display: "block", "z-Index": 900, opacity: region_cache_opacity});


			$('map[name='+rootArea.img.getAttribute('usemap').substr(1)+'] area').each(function(){
				if ($(this).attr('id')!=areaId) {
					var coords=$(this).attr('coords');
					if (coords.length) {
						$(rootArea.img).newRegion({
							coords: coords
						});
					}
				}
			});

			$(rootArea.img).newRegion({
				callback: region_callback
			});

			var region=document.region;
			region[region.length-1].context=context;
			context.area.region=region[region.length-1];

			$.notify({message: $.translate('draw_the_area_on_the_map')});

		} else {
			$.notify({message: $.translate('must_upload_map_to_edit_area')});
			return; // we don't want to continue
		}


	} else { // edit existing region

		$(context.area.htmlArea).mouseout();

		$('.cache').css({display: "block", "z-Index": 900, opacity: region_cache_opacity});

		$('map[name='+rootArea.img.getAttribute('usemap').substr(1)+'] area').each(function(){
			if ($(this).attr('id')!=areaId) {
				var coords=$(this).attr('coords');
				$(rootArea.img).newRegion({
					coords: coords
				});
			}
		});

		coords=$(context.area.htmlArea).attr('coords');
		$(rootArea.img).newRegion({
			coords: coords,
			callback: region_callback
		});
		var region=document.region;
		region[region.length-1].context=context;
		context.area.region=region[region.length-1];
		context.area.region.edit();
	}

	$('#area_toolbar').find('.save, .devices, .insert, .append, .add, .rename, .upload, .delete, .geek_mode').button('disable');
	$('#area_toolbar button.edit').button('option', 'label', $.translate('edit_area_done'));
};

// area region edit callback
function region_callback(what,event) {

	switch(what) {
	
		case 'keydown':
			switch(event.keyCode) {
				case 27: // escape
					this.context.area.region=null;
					this.dispose();
					$(document.region).each(function(i,region){
						region.dispose();
					});
					$('.cache').hide();
					$(document).unbind('.region');
					$(window).unbind('.region');
					$('#area_toolbar').find('.save, .devices, .insert, .append, .add, .rename, .upload, .delete, .geek_mode').button('enable');
					$('#area_toolbar button.edit').button('option', 'label', $.translate('edit_area'));
					areaToolbar_update();
					return false;

				case 13: // enter
					if (this.corners.length<3) {
						this.corners.splice(0,this.corners.length);
					}
					this.close({force: true});
					return false;
			};
			return true;


		case 'closed':

			$('#area_toolbar').find('.save, .devices, .insert, .append, .add, .rename, .upload, .delete, .geek_mode').button('enable');
			$('#area_toolbar button.edit').button('option', 'label', $.translate('edit_area'));

			var context=this.context;
			if (!context)
				return true;

			var isnew=(!context.area.htmlArea);

			context.area.htmlArea=this.toArea(context.area.htmlArea);
			$(context.area).attr('coords',$(context.area.htmlArea).attr('coords'));
			context.area.region=null;

			$('.cache').hide();
			var xmlArea=context.area;
			var areaId=$(xmlArea).attr('id');
			if (isnew) {
				var htmlArea=context.area.htmlArea;
				htmlArea.setAttribute('id',areaId);
				htmlArea.setAttribute('name',areaId);
				$(htmlArea).bind('click',area_click).bind('dblclick',area_click);
				htmlArea.className="area_context";
				htmlArea.setAttribute('tag','area');
				htmlArea.area=xmlArea;
				htmlArea.span=$('#area_list').find('span[area_id='+areaId+']')[0];

				if (rootArea.img)
					rootArea.img.map.insertBefore(htmlArea,(rootArea.img.map.childNodes.length?rootArea.img.map.childNodes[0]:null));

			} else {
				if ($.browser.webkit) {
					areas_parse(config,areasparse_root);
					$('#area_list')[0].style.display="block";
					$('#areas')[0].style.display="block";
				}
			}

			areamenu_setup();
			area_click.call(context.area);
			$(document.region).each(function(i,region){
				if (region.context && region.context.area)
					region.context.area.region=null;
				region.dispose();
			});
			devices_parse(config.devices,rootArea);
			$('#area_toolbar .save').button('enable');
			setTimeout(function(){
				enable_hilighting();
				$('img.map').droppable({
					drop: device_dropOnMap
				});
			}, 200);

			return false;
			break;
	};

	return true;
};

// hilight/unhilight area in #area_list
function link_dohilight(area,out) {

	if (!area) return;
	if (contextMenu) return;

	if (out) {
		$('#area_list .current').removeClass('current');
		hilitArea=null;
		return;
	};

	if (hilitArea) {
		if ($(hilitArea).attr('id')==$(area).attr('id')) {
			return;
		};
		$('#area_list .current').removeClass('current');
		hilitArea=null;
		$(area.htmlArea).mouseout();
	};

	hilitArea=area;	
	$('span[area_id="'+$(area).attr('id')+'"]').addClass('current');

};

// get config node from closest parent with .node property
function parent_node(elem) {

	if (elem.length)
		elem=elem[0];

	while(elem && elem.tagName && elem.tagName.toLowerCase()!='body') {
		if (elem.node) {
			return elem.node;
		};
		elem=elem.parentNode;
	};
	return null;
};

// get or set specified attribute for closest parent with this attribute
function parent_attr(elem,attributeName,value) {

	var attribute;

	while(elem && elem.tagName && elem.tagName.toLowerCase()!='body') {
		attribute=elem.getAttribute(attributeName);
		if (attribute) {
			if (value!=undefined)
				elem.setAttribute(attributeName,value);

			return attribute;
		};
		elem=elem.parentNode;
	};

	return null;
};

// commit parameters change
function method_commit(elem) {
	var node=parent_node(elem);
	var method_span = $(elem).closest('.method');
	var span = $(elem).closest('.value.parameter');
	var immediatekeepbutton = $(node).attr('immediatekeepbutton');

	if (node.tagName.toLowerCase() != 'immed' || !method_span || !node || !span || immediatekeepbutton === undefined)
		return;

	var param_num = method_span.find('.parameter').size();

	var attr_value=eval($(node).attr('value'));
	if (typeof(attr_value)!="object") {
		alert_dialog('method_commit: attr_value is not an array');
		return;
	}

	if (param_num == 0 || (attr_value && attr_value.length==param_num))
		parameter_send(span.attr('device'),span.attr('instance'),span.attr('commandclass'),span.attr('method'),repr_array(attr_value));
	else
		alert_dialog($.translate('select_all_params_to_commit'));
};

// represent array with number, string and array elements in reversible way: use eval('[' + return_value + ']') to rever back to an array
function repr_array(arr) {
	var repr='';
	for (var indx in arr) {
		if (repr != '')
			repr += ',';
		switch (typeof(arr[indx])) {
			case 'number':
				repr += arr[indx].toString();
				break;
			case 'string':
				repr += "'" + arr[indx].replace(/'/g, "\'") + "'"; // " // just for joe to hilight syntax properly
				break;
			case 'object':
				repr += '[' + repr_array(arr[indx]) + ']';
				break;
			default:
				if (arr[indx] === null)
					repr += 'null'; // for null object
				else
					error_msg('Unknown type of parameter: ' + typeof(arr[indx]));
		}
	};

	return repr;
};

// set device config parameter and returns new array, send command for immed gui
function parameter_set(elem,newval) {

	// get parameter number
	var parameter=parseInt(parent_attr($(elem).get(0),'parameter'));
	var node=parent_node(elem);
	var attributeName;

	switch(node.tagName.toLowerCase()) {
		case 'devicechange':
		case 'deviceconfiguration':
		case 'new_deviceconfiguration':
			attributeName="parameter";
			break;
		default:
			attributeName="value";
			break;
	};

	var attr_value=eval($(node).attr(attributeName));
	if (typeof(attr_value)!="object") {
		alert_dialog('parameter_set: attr_value is not an array');
		return;
	}

	// warn if no default value
	if (attr_value[parameter]==undefined) {
		alert_dialog('parameter_set: attr_value[parameter] undefined');
	};

	// warn if new value is not a number
	if (typeof(newval)=="number" && isNaN(newval)) {
		alert_dialog('parameter_set: newval is not a number');

	} else {
		// set node attr if value changed
		if (attr_value[parameter]==undefined || attr_value[parameter]!=newval) {
			attr_value[parameter]=newval;
			$(node).attr(attributeName, '[' + repr_array(attr_value) + ']');
			if (node.tagName.toLowerCase() != 'immed') {
				save_enable(elem);
			}
		};

		if (node.tagName.toLowerCase() == 'immed') {
			// immediately send command
			var span=$(elem).closest('span.value.parameter');
			if ($(node).attr('immediatekeepbutton') === undefined && attr_value.length == 1)
				parameter_send(span.attr('device'),span.attr('instance'),span.attr('commandclass'),span.attr('method'),repr_array(attr_value));
		}
	};

	return attr_value;
};

var parameterSend_timeout;
// send device parameter to server
function parameter_send(device,instance,commandclass,method,values_) {

	if (parameterSend_timeout)
		clearTimeout(parameterSend_timeout);

	parameterSend_timeout=setTimeout(function(){
		runCmd('devices['+device+'].instances['+instance+']'+'.commandClasses[0x'+parseInt(commandclass).toString(16)+']'+'.'+method+'('+values_+')');
	},200);
};

// get area name
function area_name(id) {

	try {
		return $(config.areas).find('area[id='+id+']').attr('name') || $.translate('Area')+' '+id+' ('+$.translate('undefined')+')';
	} catch(e) {
		return $.translate('Area')+' '+id+' ('+$.translate('undefined')+')';
	}
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

// returns the area the devices belongs to: the deepest area on which the device is placed. null if device is not attached to area
function device_area(id) {
	var l_max = 0;
	var sel_areaId = null;
	$(config.devices).find('device[device='+id+']').each(function() {
		var areaId = $(this).attr('area');
		if (areaId && area_level(areaId) >= l_max)
			sel_areaId = areaId;
	});
	return sel_areaId;
};

// returns the name of area the device belongs to. if not_attached_class is defined, the device_not_attached_to_area string will be warped in <span> with class not_attached_class
function device_area_name(id, not_attached_class) {
	var areaId = device_area(id);
	return areaId === null ? ( (not_attached_class ? '<span class="'+not_attached_class+'">' : '') + $.translate('device_not_attached_to_area') + (not_attached_class ? '</span>' : '')) : area_name(areaId);
};

// get device type
function device_type(device) {
	if (device == 255)
		return '';
	try {
		if (config.devices && $(config.devices).find('device[device='+device+']').attr('devicetype'))
			return $(config.devices).find('device[device='+device+']').attr('devicetype');
		else
			return '';
	} catch(e) {
		return '';
	}
};

// get device name
function device_name(device,options) {

	options=$.extend({
		nameOnly: false,
		withoutId: false
	},options);


	var suffix='';

	if (device == 255)
		return $.translate('all_nodes');
	try {	
		if (!ZWaveAPIData.devices[device])
			suffix=' ('+$.translate('undefined_device')+')';

		if (config.devices && $(config.devices).find('device[device='+device+']').attr('description'))
			return $(config.devices).find('device[device='+device+']').attr('description')+(options.withoutId?'':' ('+device+')'+suffix);
		else
			return $.translate('_device')+' '+device+(options.nameOnly?'':suffix);
	} catch(e) {
		return $.translate('_device')+' '+device;
	}
};

// get instance name
function instance_name(device,instance) {

	try {
		if (ZWaveAPIData.devices[device].instances[instance])
			return $.translate('Instance')+' '+instance;
	} catch (e) {
		return $.translate('Instance')+' '+instance+' ('+$.translate('undefined')+')';
	}
};

// get commandclass name
function commandClass_name(device,instance,commandClass) {

	var ret;

	try {
		ret=ZWaveAPIData.devices[device].instances[instance].commandClasses[commandClass].name;
	} catch (e) {
		ret=commandClass+' ('+$.translate('undefined')+')';
	};

	return ret;

};

// get command name
function command_name(device,instance,commandClass,methodclass,command) {

	var ret;

	if (!methodclass)
			methodclass='userSet';	

	try {
		if (ZWaveAPIData.devices[device].instances[instance].commandClasses[commandClass][methodclass][command])
			ret=$.translate(command);
		else
			ret=command+' ('+$.translate('undefined')+')';
	} catch (e) {
		ret=command+' ('+$.translate('undefined')+')';
	};

	return ret;
};

// get parameter name
function parameter_name(device,instance,commandClass,command,parameter) {
	return parameter;
};

var radiobutton_id=0;
var slider_id=0;

// build and initialize method gui
// see comment for scenes_parse()
function method_gui(settings) {
	var span=$(this);
	if (!settings) {
		settings={
			undefined: true,
			device: span.attr('device'),
			instance: span.attr('instance'),
			commandclass: span.attr('commandclass'),
			method: span.attr('method'),
			immediate: span.attr('immediate'),  // change device parameter immediately
			immediatekeepbutton: span.attr('immediatekeepbutton')
		};
	} else {
		settings=$.extend({
			immediate: false,
			immediatekeepbutton: true
		},settings);
	};

	span=$(span);
	if (settings.undefined || settings.immediate) {
		span.attr('device',settings.device);
		span.attr('instance',settings.instance);
		span.attr('commandclass',settings.commandclass);
		span.attr('method',settings.method);
	};	

	try {
		var _gui=method_rawGui(getMethodSpec(settings.device, settings.instance, settings.commandclass, settings.method), settings);
	} catch(e) {
		console.log(e);
		span.prev().html('');
		span.html('');
		return;
	};

	var gui=$('<span class="method">'+_gui.html+'</span>');

	var values_=eval(span.attr('value'));

	if (settings.immediate) {
		var node = document.createElement('immed');
		$(node).attr('device',settings.device);
		$(node).attr('instance',settings.instance);
		$(node).attr('commandclass',settings.commandclass);
		$(node).attr('method',settings.method);
		$(node).attr('value', span.attr('value'));
		if (_gui.count > 1 || settings.immediatekeepbutton) {
			$('<button class="commit">'+$.translate(settings.method)+'</button>').prependTo(gui);
			$(node).attr('immediatekeepbutton', '');
		};
		span.get(0).node = node;
	};

	if (typeof(values_)!="object") {
		alert_dialog('method_gui: values is not an array');
		return;
	}

	method_guiInit(gui,values_);

	span.html('');
	span.append(gui);

	method_guiPrepareLabels(_gui, gui, !settings.immediate || settings.immediatekeepbutton || !_gui.count == 1, true);
};

function method_guiCC(method_descriptor) {
	// memorize last commandclass and command not to re-draw the interface if they don't change
	$(this.parentNode).attr('commandclass', method_descriptor.commandclass.toString(16)).attr('method', method_descriptor.method);

	method_gui.call(this, method_descriptor);
};

function method_guiPrepareLabels(_gui, gui, keep_labels, wrap_div, append_prepend) {
	var span = gui.parent();
	span.prev().hide();
	var params=$(gui).find('.parameter');
	if (wrap_div)
		params.wrap($('<div class="attribute"/>'));
	if (keep_labels) {
		for (var i=0; i<params.size(); ++i) {
			var label=span.prev().clone().html(_gui.labels[i]).show();
			if (append_prepend)
				$(params[i]).append(label);
			else
				$(params[i]).prepend(label);
		}
	}
};

// returns array with default values: first value from the enum, minimum value for range, empty string for string, first nodeId for node, default schedule for the climate_schedule
function method_defaultValues(method) {
	function method_defaultValue(val) {
		if ('enumof' in val['type']) {
			if (val['type']['enumof'][0])
				return method_defaultValue(val['type']['enumof'][0]); // take first item of enumof
			else
				return null;
		}
		if ('range' in val['type'])
			return val['type']['range']['min'];
		if ('fix' in val['type'])
			return val['type']['fix']['value'];
		if ('string' in val['type'])
			return "";
		if ('node' in val['type'])
			for (var dev in ZWaveAPIData.devices) {
				if (devices_htmlSelect_filter(null,dev,'node')) {
					continue;
				};
				return parseInt(dev);
			};
		alert_dialog('method_defaultValue: unknown type of value');
	};

	var parameters = [];
	method.forEach(function(val,parameter_index){
		parameters[parameter_index] = method_defaultValue(val);
	});

	return parameters;
};

// init method gui widgets
function method_guiInit(gui,values_) {
	// Setup and init sliders and radiobuttons, and bind "onchange" event handlers to every gui widget so that 
	// the attribute "value" of the widget closest parent with class 'value' is set to table [param1, param2, ...]
	// Here we pretend to have correct values in values_

	var i=0;

	// Check if values_ is an array of size equal to size of parameters.
	if (!values_ || values_.length != gui.find('.parameter').size()) {
		alert_dialog("method_guiInit: values[] size differs from number of parameters!");
	};

	gui.find('.parameter').each(function(){

		$(this).find('.slider:not(.bitrange)').each(function() {

			// create slider
			$(this).slider({
				min: parseInt($(this).attr('min'), 10),
				max: parseInt($(this).attr('max'), 10),
				slide: function(event,ui) {
					var _val = parseInt(ui.value, 10) + parseInt($(this).attr('shift') || 0, 10);
					parent_attr(this,'value',_val);
					$(this.parentNode).find('[slider="'+$(this).attr('slider')+'"]').attr('value',ui.value);
					if (parent_node(this)) {
						parameter_set(this,_val);
					}

				},
				change: function(event,ui) {
					var _val = parseInt(ui.value, 10) + parseInt($(this).attr('shift') || 0, 10);
					parent_attr(this,'value',_val);
					$(this.parentNode).find('[slider="'+$(this).attr('slider')+'"]').attr('value',ui.value);
					if (parent_node(this)) {
						parameter_set(this,_val);
					}
				},
				start: function(event) {
					$(this.parentNode).find('input.range[slider=' + $(this).attr('slider') + ']').attr('checked',true);
				}
			});

			// slider handle height
			$(this).find('.ui-slider-handle').css({height: '1.75em'});

			// we don not set initial position by default: this cause save button to be enables and the slider initial value to be saved.
			// instead we prefer to set initial value on radio click if not value selected before

			// initial position if this slider is selected by the enum
			if (values_[i]>=(parseInt($(this).attr('min'), 10) + parseInt($(this).attr('shift') || 0, 10)) && values_[i]<=(parseInt($(this).attr('max'), 10) + parseInt($(this).attr('shift') || 0, 10))) {
				$(this.parentNode).find('[slider="'+$(this).attr('slider')+'"]').attr('value',values_[i]);
				$(this.parentNode).find('input.range[slider=' + $(this).attr('slider') + ']').attr('checked',true);
				$(this).slider('value',values_[i]);
			}
		});

		// initial radio button - should be after slider to always select fix value even if overlaping with slider values
		$(this).find('input.fix').each(function(){
			if (parseInt($(this).attr('value'), 10)==values_[i]) {
				$(this).parent().parent().find('input:checked').attr('checked',false);
				$(this).attr('checked',true);
			};
		});

		// initial string
		$(this).find('span.string').each(function() {
			$(this).attr('value', values_[i]);
			// there should be never node inside of enumof // $(this.parentNode).find('input.string').attr('checked',true);
		});

		// initial node
		$(this).find('span.device.node').each(function() {
			$(this).attr('value', values_[i]);
			// there should be never node inside of enumof // $(this.parentNode).find('input.node').attr('checked',true);
		});

		// initial climate_schedule
		$(this).find('span.climate_schedule').each(function() {
			$(this).bind('click', schedule_gui);
			$(this).attr('value', '[' + repr_array(values_[i]) + ']');
			// there should be never climate_schedule inside of enumof // $(this.parentNode).find('input.node').attr('checked',true);
		});

		$(this).find('input.bitcheck').each(function() {
			$(this).attr('checked', ((eval(parent_attr(this, 'value'))[parent_attr(this, 'parameter')] & (1 << $(this).attr('bit'))) != 0));
		});

		$(this).find('.slider.bitrange').each(function() {

			// create slider
			$(this).slider({
				min: parseInt($(this).attr('min'), 10),
				max: parseInt($(this).attr('max'), 10),
				slide: function(event,ui) {
					var cur_value = eval(parent_attr(this.parentNode, 'value'))[parent_attr(this, 'parameter')]; // parent_attr refers the slider, while parent_attr(this.parentNode, ) to the correct node
					var bit_value = ((1 << ($(this).attr('bit_to') - $(this).attr('bit_from') + 1)) - 1) << $(this).attr('bit_from');
					var new_value = cur_value - (cur_value & bit_value) + (ui.value << $(this).attr('bit_from'));
					parent_attr(this.parentNode,'value','[' + repr_array(parameter_set(this,new_value)) + ']');// parent_attr refers the slider, while parent_attr(this.parentNode, ) to the correct node
					$(this.parentNode).find('[slider="'+$(this).attr('slider')+'"]').attr('value',ui.value);
				},
				change: function(event,ui) {
					var cur_value = eval(parent_attr(this.parentNode, 'value'))[parent_attr(this, 'parameter')]; // parent_attr refers the slider, while parent_attr(this.parentNode, ) to the correct node
					var bit_value = ((1 << ($(this).attr('bit_to') - $(this).attr('bit_from') + 1)) - 1) << $(this).attr('bit_from');
					var new_value = cur_value - (cur_value & bit_value) + (ui.value << $(this).attr('bit_from'));
					parent_attr(this.parentNode,'value','[' + repr_array(parameter_set(this,new_value)) + ']'); // parent_attr refers the slider, while parent_attr(this.parentNode, ) to the correct node
					$(this.parentNode).find('[slider="'+$(this).attr('slider')+'"]').attr('value',ui.value);
				},
				value: (values_[i] >> $(this).attr('bit_from')) & ((1 << ($(this).attr('bit_to') - $(this).attr('bit_from') + 1)) - 1)
			});

			// slider handle height
			$(this).find('.ui-slider-handle').css({height: '1.75em'});

			// initial position in input
			$(this.parentNode).find('[slider="'+$(this).attr('slider')+'"]').attr('value',(values_[i] >> $(this).attr('bit_from')) & ((1 << ($(this).attr('bit_to') - $(this).attr('bit_from') + 1)) - 1));
		});

		++i;
	});

	gui.find('.value.node').each(function(){
		devices_htmlSelect(this);
	});

	gui.find('input.bitcheck').bind('click', bitcheck_click);

	input_bindings(gui);

	gui.find('label, input').bind('click',function(event){
		event.stopPropagation();
		return true;
	});

	gui.find('label, input').bind('dblclick',function(event){
		event.stopPropagation();
		return true;
	});

	gui.find('input.sliderPos').bind('click',function(event){
		var radio=$(this).parent().find('input.range[slider=' + $(this).attr('slider') + ']');
		if (!radio.checked) {
			radio.click();
			save_enable(radio);
		};
		return true;
	});

	gui.find('input.sliderPos,input.bitrangePos').bind('keydown',function(event){
		var k=event.keyCode;
		// END: 35 HOME: 36
		// ARROWS: 37 <- 38 ^ 39 -> 40 V
		// TAB: 9
		// F5: 116
		// DEL: 46 BSP: 8
		// 0-9: 48-57
		// ENTER: 13
		if (event.ctrlKey || (k>=35 && k<=40) || k==9 || k==116 || (k==46 || k==8) || (k>=48 && k<=57) || k==13) {
			return true;
		} else {
			return false;
		}		
	}).bind('change',function(event){
		var value=parseInt($(this).attr('value'), 10);
		var max=parseInt($(this).prev().attr('max'), 10);
		var min=parseInt($(this).prev().attr('min'), 10);
		if (value<min) $(this).attr('value',min);
		if (value>max) $(this).attr('value',max);
		$(this.parentNode).find('.slider[slider=' + $(this).attr('slider') + ']').slider('value',$(this).attr('value'));
		return true;
	});

	gui.find('select.device').bind('change', function(event) {
		$(this).parent().parent().find('input.node').attr('checked', true);
	});

	gui.find('button.radio').bind('click',function(event){
		radio_click.call(this,event);
		return false;
	});

	gui.find('button.commit').bind('click',function(event){
		method_commit(this);
	});

};

// show values in method gui
function method_guiShow(gui,values_) {

	var i=0;

	gui.find('.parameter').each(function(){

		var parameter=$(this);
		var parameter_num=parseInt($(this).attr('parameter'), 10);
		var found=false;

		parameter.find('label').removeClass('current_val'); // remove current underline
		parameter.removeClass('current_val'); // remove current underline
		parameter.find('label.slider_current_val').remove(); // remove previous label of slider made by this function

		parameter.find('.fix').each(function(){
			if (values_ && $(this).attr('value')==values_[parameter_num]) {
				$(this.parentNode).addClass('current_val');
				found=true;
			}
		});

		parameter.find('.slider:not(.bitrange)').each(function() {
			if (values_ && !found && values_[parameter_num]>=(parseInt($(this).attr('min'), 10) + parseInt($(this).attr('shift') || 0, 10)) && values_[parameter_num]<=(parseInt($(this).attr('max'), 10) + parseInt($(this).attr('shift') || 0, 10))) {
				$('<label></label>').addClass('current_val').addClass('slider_current_val').html('('+(values_[parameter_num] - parseInt($(this).attr('shift') || 0, 10))+')').insertAfter($(this).next());
				found=true;
			}
		});

		parameter.find('.bitrange.slider').each(function() {
			if (values_)
				$('<label></label>').addClass('current_val').addClass('slider_current_val').html('('+((values_[parameter_num] >> $(this).attr('bit_from')) & ((1 << ($(this).attr('bit_to') - $(this).attr('bit_from') + 1)) - 1))+')').insertAfter($(this).next());
		});

		parameter.find('.bitcheck').each(function() {
			if (values_ && ((values_[parameter_num] >> $(this).attr('bit')) & 0x01))
				$(this.parentNode).addClass('current_val');
		});

		parameter.find('.value.node').each(function(){
			if (values_)
				$(this).find('option[value="'+values_[parameter_num]+'"]').addClass('current_val');
		});

		parameter.find('input.string').each(function(event){
			// !!!!
			alert_dialog('This code is not tested yet - contact software developers for more info.');
			if (values_)
				$(this).parent().previous().text(' ('+values_[parameter_num]+')');
		});

		parameter.find('input.climate_schedule').each(function(event){
			// !!!!
			alert_dialog('This code is not tested yet - contact software developers for more info.');
			if (values_)
				$(this).parent().previous().text(' ('+values_[parameter_num]+')');
		});
	});
};

$.fn.select=function(value) {
	
	var option=$(this).find('option[value="'+value+'"]');
	
	if (!option.size())
		return this;

	this[0].selectedIndex=option[0].index;

	return this;
};

// parse device icon config
function devices_parse(config,root) {
	// note that here local config is global config.devices !

	// first of all we remove devices wich no longer exist
	var deleted_devices = array_unique($(config).find('device').filter(function() { return !($(this).attr('device') in ZWaveAPIData.devices); }).map(function() { return $(this).attr('device'); }));
	if (deleted_devices.length > 0) {
		confirm_dialog($.translate('would_you_like_delete_device_from_map') + '<br/><br/>' + $.map(deleted_devices, function(devId) { return device_name(devId); }).join(',<br/>'), $.translate('device_deleted'), function() {
			$.each(deleted_devices, function (index, devId) {
				$(config).find('device[device=' + devId + ']').remove();
			});
			$('#area_toolbar .save').button('enable');
			deviceList_update(); // we restart this function to redraw the device on the map, since confirm_dialog is asynchroneous (all devices to be deleted was already drawn while user was reading the confirm_dialog message.
		});
	};

	var areas=$('#areas');
	var imgOffset=$('img.map').offset();

	if (!imgOffset)
		return;

	orphanDeviceList_update();

	areas.find('.device_icon').remove();

	$(config).find('device[rootarea='+$(root).attr('id')+']').each(function() {

		var device=$(this);
		var devnum=$(device).attr('device');

		var top=parseInt(device.attr('top'))+imgOffset.top;
		var left=parseInt(device.attr('left'))+imgOffset.left;

		var icon=device_icon(devnum, false);

		deviceIcon_add2gui(areas,icon,top,left);
		deviceIcon_clip(areas,icon,left,top);
	});
};

// show/hide device icon on boundaries
function deviceIcon_clip(areas,icon,left,top) {

	if (rectInElement(areas[0],left+23,top+20,icon.width(),icon.height())) {
		icon.css({position: 'absolute', top: top, left: left}).show();	 
	} else {
		icon.hide();
	}
};

// get attribute attributeName or "value" from selected menu item
$.fn.getSelection=function(attributeName) {

	if (!this[0].options)
		return null;

	var count=this[0].options.length;

	if (!count)
		return null;
	
	return $(this[0].options[this[0].selectedIndex]).attr(attributeName||'value');

};

// converts DOM tree to pseudo XML
$.fn.dom2text=function() {

	var text='';

	this.each(function() {
		if (window.ActiveXObject) {
			text+=this.xml;
		} else {
			text+=(new XMLSerializer()).serializeToString(this);
		}
	});

	return text.replace(/ xmlns="[^"]+"/g,''); // " // for joe editor to work with hilight
};

// check if area is selected
function selectedArea_has(value) {
	return (selectedArea.indexOf(parseInt(value))>=0);
};

// init dialog position and geometry, set onresize handler and translate buttons
function dialog_init(elem,aspect) {

	elem=$(elem.parentNode);

	if (elem.width()>($(document.body).width()-128)) {
		elem.css({left: 64, width: $(document.body).width()-128});

	};

	if (aspect) {
		var width=elem.width();
		var height=elem.height();
		if (width < height*aspect) {
			var left=parseInt(elem.css('left'));
			elem.css({width: height*aspect});
			left-=(height*aspect-width)/2;
			elem.css({left: left});
		}
	};

	dialog_buttons_translate();

	elem.center({onresize: true}).focus();	
};

function external_links(content) {
	$(content).find('a.external_link').each(function() {
		$(this).attr('target', '_blank');
	});
};
