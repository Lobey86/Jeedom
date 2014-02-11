/*
 * Global Stylesheet jQuery Plugin
 * Version: 1.0
 * 
 * Enable CSS modification through stylesheets, rather than inline CSS.
 *
 * Copyright (c) 2009 Jeremy Shipman (http://www.burnbright.co.nz)
 * Copyright (c) 2010 Luc Deschenaux (luc.deschenaux(a)freesurf.ch)
 * 
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 * 
 * INSTRUCTIONS:
 *
 * When no matching property is found in matching stylesheet rules selectors,
 * rule is added to the specified or default global stylesheet (specified with
 * options.title, defaults to "globalStyleSheet") which is
 * created automatically.
 *
 * Note that jQuery selection may include elements for which no matching
 * css rule selector can been found in stylesheets !
 * eg: "div" will not match "div.someclass" nor "div, p"
 * 
 * When no operation is performed on DOM items matching the css rule selector
 * the following syntax is recommended to avoid overhead:
 *    $.globalcss("selector",...);
 *
 * Use in the same way as the jQuery css function. Eg:
 *    $.globalcss(selector,"property","value");
 *    $.globalcss(selector,{property: "value", property: "value"});
 *    $.globalcss(selector,"property"); // get value
 *    or
 *    $(selector).globalcss("property","value");
 *    $(selector).globalcss({property: "value", property: "value"});
 *    $(selector).globalcss("property"); // get value
 *
 * For non default options you need to create stylesheets manually:
 *    $.globalstylesheet({media: 'print', title: "mySheet"});
 *
 * When using non default title, append the title to the parameter list:
 *    $(selector).globalcss("property","value",'mySheet');
 *    $(selector).globalcss({property:"value", property: 0},'mySheet');
 *    $(selector).globalcss("property",'mySheet'); // get value
 *
 * The .get() or .print() methods return the css as an object or a string:
 *    $.globalstylesheet('globalStyleSheet').print()
 *
 * The .dispose() method remove the stylesheet,
 * but you can set .sheet.disabled to true instead.
 *
 * When options.firstOnly is true, all the stylesheet rules with a
 * matching selector and property are changed.
 *
 * When options.jQuery_css is true jQuery.css() is called for the
 * DOM items matching the css rule selector
 *
 * Remember that jQuery selection may include elements for which no matching
 * css rule selector can been found in stylesheets !
 * eg: "div" will not match "div.someclass" nor "div, p"
 *
 */

(function($) {

	if (!document.styleSheets) {
		alert("document.Stylesheets not found");
		return false;
	};

	if (!StyleSheet)  {
		alert("StyleSheet not found");
		return false;
	};

	function findGlobalStylesheet(title,callback) {

		if (document.globalstylesheet) {

			for (var i=0; i<document.globalstylesheet.length; ++i) {

				if (document.globalstylesheet[i].options.title==title) {

					if (callback) {
						return callback(i);

					} else {
						return document.globalstylesheet[i];
					}
				}
			}
		}
	};

	function getStylesheet(title) {

		if (typeof(title)!= "string") {
			return null;
		};

		for (var i=0; i<document.styleSheets.length; ++i) {

			var owner=document.styleSheets[i].ownerNode||document.styleSheets[i].owningElement;

			if (owner && owner.title==title) {
				return document.styleSheets[i];
			}
		};

		return null;
	};

	var globalstylesheet = function(options) {

		var defaults={
			rel: 'stylesheet',
			media: 'screen',
			title: 'globalStyleSheet',
			firstOnly: true,
			jQuery_css: false // call also jQuery.css for selector
		};

		this.init=function() {

			var node=document.createElement('style');

			node.type="text/css";
			node.rel=this.options.rel;
			node.media=this.options.media;
			node.title=this.options.title;

			document.getElementsByTagName("head")[0].appendChild(node);

			return getStylesheet(this.options.title);
		};

		this.dispose=function() {

			findGlobalStylesheet(this.options.title,function(idx){
				document.globalstylesheet[idx]=null;
				document.globalstylesheet.splice(idx,1);
			});

			var owner=this.sheet.ownerNode||this.sheet.owningElement;

			try {
				owner.parentNode.removeChild(owner);

			} catch(e) {
				alert('cannot remove stylesheet: '+e);
			};
		};

		this.setRules=function(selector, properties) {

			var rules=this.rules_cache[selector]=this.getRules(selector);

			if (rules.length==0) {

				if (typeof properties=="string") {
					return '';
				};

				this.insertRules(selector,properties);

			} else {
				if (typeof properties=="string") {
					for (var i=0; i<rules.length; ++i) {
						var style=rules[i].style[this.removeHyphen(properties)];
						if (style!=undefined)
							return style;
					};
					return '';

				} else {
					var notyetdefined;
					for (var name in properties) {
						name=this.removeHyphen(name);
						var found=false;
						for (var i=0; i<rules.length; ++i) {
							if (rules[i].style[name]!=undefined) {
								rules[i].style[name]=properties[name];
								found=true;
								if (this.options.firstOnly)
								       	break;
							}
						};
						if (!found) {
							if (!notyetdefined) {
								notyetdefined=new Object;
							};
							notyetdefined[name]=properties[name];
						}
					};

					if (notyetdefined) {
						this.insertRules(selector,notyetdefined);
					}
				}
			} 

		};
	
		this.insertRules=function(selector,properties) {

			var propstr='';
			for (var name in properties) {
				propstr+=name+': '+properties[name]+'; ';
			};

			if (this.sheet.insertRule) {
				this.sheet.insertRule(selector+'{'+propstr.trim()+'}',this.sheet.cssRules.length);

			} else { // IE
				this.sheet.addRule(selector,propstr.trim(),-1);
			};

			this.rules_cache[selector] = this.getRules(selector);
		};

		this.removeHyphen=function(str) {

			var terms=str.split('-');

			for (var i=1; i<terms.length; ++i) {
				terms[i]=terms[i].substr(0,1).toUpperCase()+terms[i].substr(1);
			};

			return terms.join('');
		};
	
		this.getRules=function(selector) {

			if (this.rules_cache[selector] != undefined && this.rules_cache[selector].length) {
				return this.rules_cache[selector];

			} else {
				var rules_list=new Array;
				var sheets = document.styleSheets;
				for (var s=0; s<sheets.length; s++) {
					var rules = sheets[s].cssRules || sheets[s].rules;
					for(var r = 0; r < rules.length; r++) {
						if (rules[r].selectorText == selector) {
							rules_list.push(rules[r]);
						}
					}
				};
				return rules_list;
			}
		};
		
		this.get=function() {

			var styleinfo = null;
			var rules = this.sheet.cssRules || this.sheet.rules;

			if (rules.length) {
				styleinfo = new Object;
				for(var i = 0; i < rules.length; i++) {
					styleinfo[rules[i].selectorText]=rules[i].style.cssText.trim();
				}
			};

			return styleinfo;
		};
		
		this.print=function() {

			var styleinfo = '';
			var rules = this.sheet.cssRules || this.sheet.rules;

			if (rules.length) {
				for(var i = 0; i < rules.length; i++) {
					styleinfo+='\n';
					styleinfo+=rules[i].selectorText+' {';
					rules[i].style.cssText.split(';').forEach(function(property){;
						property=property.trim();
						if (property.length) {
							styleinfo+='\n\t'+property+';';
						}
					});
					styleinfo+='\n}\n';
				}
			};

			return styleinfo;
		};
		
		this.css=function(jqueryObject,args) {

			var rules;

			if (typeof args[0] == 'object') {
				rules=args[0];

			} else {
				if (args.length==1) {
					rules=args[0];

				} else {
					rules=new Object;
					rules[args[0]]=args[1];
				}
			};

			var ret=this.setRules(jqueryObject.selector,rules);

			if (typeof rules=='string') {
				return ret;

			} else {
				if (this.options.jQuery_css && (this.options.media=="screen" || this.options.media=="all")) {
					if (jqueryObject.css) {
						return jqueryObject.css(rules);

					} else {
						return $(jQueryObject.selector).css(rules);
					}

				} else {

					return jqueryObject;
				}
			}
		};

		this.options=$.extend(defaults,options);

		if (findGlobalStylesheet(this.options.title)) {
			alert('Duplicate stylesheet name: '+this.options.title);
			return null;
		};

		this.rules_cache=new Array;
		this.sheet=this.init();

		if (this.sheet) {
			if (!document.globalstylesheet) {
				document.globalstylesheet=new Array;
			};
			document.globalstylesheet.push(this);
			return this;

		} else {
			return null;
		}
	};

	$.globalstylesheet=function(options) {

		if (typeof(options)=='string') {
			return findGlobalStylesheet(options);
		};

		return new globalstylesheet(options);
	};

	$.fn.globalcss=function() {

		// when last argument is a stylesheet name, use this stylesheet 
		if (arguments.length>1) {

			var lastarg=arguments[arguments.length-1];
			if (typeof(lastarg)=='string') {

				var gss=findGlobalStylesheet(lastarg);
				if (!gss) {
					if ((arguments.length==2 && typeof(args[0])=='object') || arguments.length==3) {
						gss=new globalstylesheet({title: lastarg});
					}
				};

				if (gss) {
					var args=new Array;
					for (var i=0; i<arguments.length-1; ++i) {
						args.push(arguments[i]);
					};
					return gss.css(this,args);
				}
			}
		};

		var gss=findGlobalStylesheet('globalStyleSheet');
		if (!gss) {
			gss=new globalstylesheet;
		};

		return gss.css(this,arguments);
	};

	$.globalcss=function(selector) {
		var args=new Array;
		for (var i=1; i<arguments.length; ++i) {
			args.push(arguments[i]);
		};
		$.fn.globalcss.apply({selector: selector},args);
	};

})(jQuery);
