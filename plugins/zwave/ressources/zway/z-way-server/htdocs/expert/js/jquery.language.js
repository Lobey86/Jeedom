/*
 *   jquery.language.js - jquery plugin for multilanguage support.
 *   
 *   Copyright (C) 2010-2011 Luc Deschenaux - luc.deschenaux(a)freesurf.ch
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

/*
EXAMPLE:
 language.en.js:
	translation = {
		myspan: "My Span",
		...
	};

 static content:	
	<html>
		<head>
			<script src="jquery.language.js"></script>
			<script>
		       		$(document).ready(function(){
		       			$(document.body).language_set('en');
				};
		  	</script>
		</head>
		<body>
			<span class="intl" id="myspan"></span>
			<span class="intl" title="myspan"></span>
		</body>
	</html>

 dynamic content:
	<script>
		var node=$('<span class="intl" id="myspan"></span>');
		node.translate();
		$(document.body).append(node);
	</script>

 check: 
 	$.checktranslate('en','de','fr')

*/

(function($) {

	var _lang;
	var _selector='.intl';
	var translation=new Object();

	// load language file and set innerHTML for nodes matching selector (default: '.intl')
	$.fn.language_set=function(lang,selector) {

		_lang=lang;

		if (selector)
			 _selector=selector;

		var _this=this;

		$.ajax({
			type: 'GET',
			url: 'js/language.'+lang+'.js',
			dataType: 'json',
			async: false,
			success: function(data){
				translation = data;
				_this.translate();
			},
			error: function(err){
				alert('Error loading translation file ' + 'js/language.'+lang+'.js\n\n' + err.responseText);
			}
		});

		return this;
	};

	// set text for selected nodes

	$.fn.translate=function(custom_translation) {

		this.each(function(){

			$(this).find(_selector).add($(this).filter(_selector)).each(function(){
				this.innerHTML = custom_translation && custom_translation[this.getAttribute('id')] || (translation && translation[this.getAttribute('id')] || this.getAttribute('id'));
				if (this.getAttribute('title') && custom_translation && custom_translation[this.getAttribute('title')])
					this.setAttribute('title', custom_translation[this.getAttribute('title')]);
				if (this.getAttribute('title') && translation && translation[this.getAttribute('title')])
					this.setAttribute('title', translation[this.getAttribute('title')]);
			});

		});

		return this;
	};

	$.translate=function(text) {
		return translation[text]||text;
	}

	$.checktranslate=function() {
		var win=window.open('');
		var language=arguments;
		var done=new Array;
		$(win).ready(function() {
			win.document.write('<table cellspacing="0" cellpadding="0" style="display: none"></table>');
			for (var l=0; l<language.length; ++l) {
				done[language[l]]=new Array;
				$.ajax({
					type: 'GET',
					url: 'js/language.'+language[l]+'.js',
					dataType: 'json',
					async: false,
					success: function(data){
						var translation0=data;
						for (var k=0; k<language.length; ++k) {
							if (k==l) continue;
							$.ajax({
								type: 'GET',
								url: 'js/language.'+language[k]+'.js',
								dataType: 'json',
								async: false,
								success: function(data){
									var translation1=data;
									var table=$('table',win.document.body);
									for (idx in translation1){
										if (!translation0[idx]) {
											if (done[language[l]][idx]) continue;
											done[language[l]][idx]=true;
											table.append('<tr><td nowrap>'+language[l]+'</td><td nowrap>'+idx+'</td><td><textarea></textarea></td></tr>');
											$('textarea:last',table).text(translation1[idx]).width('100%').parent().width('100%');
										}
									};
	
								}
							});
						}
					}
				});
			}
			win.document.close();
			$(win.document.body).find('td, tr').css({ border: "1px solid black", padding: 4});
			$('table',win.document.body).show().css({border: "2px solid black"});
		});
	};

}) (jQuery);
