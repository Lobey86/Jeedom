/*
 *   jquery.notify.js - jquery plugin to display notification messages,
 *   based on jquery.jbar.js.
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

(function($) {
	
	$.notify=function(options) {
		var opts=$.extend({}, $.notify.defaults, options);

		var o=opts;
			
		if(!$('.jnotify').length){

			var _message_span=$(document.createElement('span')).addClass('jnotify-content').html(o.message);
			_message_span.css({"color" : o.color});

			var _wrap_notify=$(document.createElement('div')).addClass('jnotify jnotify-'+o.position);
			_wrap_notify.css({"background-color": o.background_color});

			if (o.removebutton){
				var _remove_cross=$(document.createElement('a')).addClass('jnotify-cross');
				_remove_cross.bind('click',$.notify.dispose);
			} else {				
				_wrap_notify.css({cursor: "pointer"});
				_wrap_notify.bind('click',$.notify.dispose);
			}	
			_wrap_notify.append(_message_span).append(_remove_cross).hide().insertBefore($('.content')).fadeIn('fast');
			timeout=setTimeout('$.notify.dispose()',o.time);
		}
	};

	var timeout;

	$.notify.dispose=function(event) {
		if($('.jnotify').length){
			clearTimeout(timeout);
			$('.jnotify').fadeOut('fast',function(){
				$(this).remove();
			});
		}	
	};

	$.notify.defaults={
		background_color 	: '#FFFFFF',
		color 			: '#000',
		position	 	: 'bottom',
		removebutton     	: false,
		time		 	: 5000	
	};
	
})(jQuery);
