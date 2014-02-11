/*
 *   jquery.scrollpos.js - jquery plugin to save and restore scrollbar position
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

	$.fn.scrollpos_save=function() {

		this.each(function() {
			$(this).bind('scroll',function(){
				$.cookie($(this).attr('id')+'.scrollTop',this.scrollTop);
				$.cookie($(this).attr('id')+'.scrollLeft',this.scrollLeft);
			});
		});
		
		return this;
	};

	$.fn.scrollpos_restore=function() {

		this.each(function() {

			var id=$(this).attr('id');

			var scrollTop=$.cookie(id+'.scrollTop');
			var scrollLeft=$.cookie(id+'.scrollLeft');

			if (scrollTop) this.scrollTop=scrollTop;
			if (scrollLeft) this.scrollLeft=scrollLeft;
		});

		return this;
	}

}) (jQuery);

