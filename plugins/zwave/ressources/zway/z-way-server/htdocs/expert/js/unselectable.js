/**
*
*  Unselectable text
*  http://www.webtoolkit.info/
*
**/

//(function($){

var Unselectable = {
 
	enable : function(e) {
		var e = e ? e : window.event;
 
		if (e.button != 1) {
			if (e.target) {
				var target = e.target;
			} else if (e.srcElement) {
				var target = e.srcElement;
			};
 
			var tag = target.tagName.toLowerCase();
			if (
				(!$(target).hasClass('selectable')) &&
			       	(!$(target).hasClass('ui-draggable')) &&
			       	(tag!='select') &&
			       	(tag!="input") &&
			       	(tag!="textarea") &&
			       	(!$(target).hasClass('draggable')) &&
			       	(target.contentEditable!="true") &&
			       	(!target.parentNode || target.parentNode.contentEditable!="true")
			) {
				// allow mousedown for firefox (useful for link based buttons)
				if (typeof(target.style.MozUserSelect)!='undefined') {
					$('body').css('MozUserSelect',"none");
					return true;
				}
				return false;
			}
		};
		return true;
	},
 
	disable : function () {
		$('body').css('MozUserSelect','');
		return true;
	}
 
};
 
if (typeof(document.onselectstart) != "undefined") {
	$(document).bind('selectstart.unselectable', Unselectable.enable);
} else {
	$(document).bind('mousedown.unselectable', Unselectable.enable);
	$(document).bind('mouseup.unselectable', Unselectable.disable);
}

//})(jQuery);
