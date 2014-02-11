/*
	jQuery Simple Drop-Down Menu Plugin
	http://javascript-array.com/scripts/jquery_simple_drop_down_menu/

	Patched by Poltorak Serguei
*/


(function($) {
	var timeout    = 500;
	var closetimer = null;
	var ddmenuitem = 0;

	function jsddm_open() {
		jsddm_canceltimer();
		jsddm_close($(this).find('ul'));
		ddmenuitem = $(this).find('ul').css('display', 'block');
	};

	function jsddm_close(to_be_opened) {
		if(ddmenuitem && ddmenuitem != to_be_opened)
			ddmenuitem.css('display', 'none');
	};

	function jsddm_timer() {
		closetimer = window.setTimeout(jsddm_close, timeout);
	};

	function jsddm_canceltimer() {
		if(closetimer) {
			window.clearTimeout(closetimer);
			closetimer = null;
		}
	};

	$.fn.jsddm = function(defaultItem, onSelect) {
		function selectItem(tabId) {
			tab_container.find('.tab:not(' + tabId + '):visible').hide();
			tab_container.find(tabId + ':not(:visible)').fadeIn();
			onSelect($(tabId));
			$.cookie('jsddm_selected', tabId);
		};
		var tab_container = $(this);
		tab_container.find('.tab').hide();
		tab_container.find('.jsddm > li').bind({
			mouseover: function() {
				if ($(this).parent().hasClass('jsddm'))
					jsddm_open.call(this);
			},
			mouseout: function() {
				if ($(this).parent().hasClass('jsddm'))
					jsddm_timer.call(this);
			},
			click: function() {
				if ($(this).parent().hasClass('jsddm')) {
					jsddm_open.call(this);
					return false; // stop propagation
				}
			}
		});
		tab_container.find('.jsddm li ul').bind({
			click: function() {
				return false;
			}
		});
		tab_container.find('.jsddm li a').bind('click', function() {
			var tab_id = $(this).attr('href');
			if (tab_id == '#')
				return;
			jsddm_close();
			selectItem(tab_id);
		});
		var savedItem = $.cookie('jsddm_selected')
		selectItem(savedItem ? savedItem : defaultItem);
	};

	document.onclick = jsddm_close;
})(jQuery);
