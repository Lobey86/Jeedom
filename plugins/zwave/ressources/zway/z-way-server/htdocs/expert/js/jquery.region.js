/*
 *   region - jquery plugin to design and edit regions
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

	var zIndex=1000;
	var region_id=0;

	function clip(val,max) {
		if (val<0) return 0;
		if (val>=max) return max-1;
		return val;
	};

	function region(area,settings) {

		this.id=region_id++;
		this.settings=$.extend({},settings);
		this.area=area;
		this.callback=settings.callback;
		this.corners=new Array;
		if (settings.zIndex) zIndex=settings.zIndex;

		// create region in design mode
		this.init=function() {

			this.mode='design';

			// region
			this.canvas=document.createElement('canvas');
			var canvas=this.canvas;
			canvas.region=this;
			canvas.className=this.settings.className;
			canvas.ctx=canvas.getContext('2d');

			var area=$(this.area);
			canvas.width=area.width(); // dimensions are 0 with chrome !!!! trigger resize as workaround 
			canvas.height=area.height();

			this.area.parentNode.appendChild(canvas);

			var position=area.position();
			$(canvas).css({position: "absolute", top: position.top, left: position.left, zIndex: zIndex++});

			// next segment
			canvas.child=document.createElement('canvas');
			canvas.child.region=this;
			canvas.child.className='next_segment';
			canvas.child.ctx=canvas.child.getContext('2d');
			canvas.child.width=canvas.width;
			canvas.child.height=canvas.height;

			$(canvas.child).css({position: "absolute", top: position.top, left: position.left, zIndex: zIndex++});

			this.area.parentNode.appendChild(canvas.child);

			$(canvas.child).bind('mousedown.region', {region: this}, this.mousedown);
			$(canvas.child).bind('dblclick.region', {region: this}, this.dblclick);
		};

		this.resizeTimeout=undefined;
		this.resize=function(event) {

			var region=this;
			if (event && event.data) 
				region=event.data.region;

			if (region.resizeTimeout) 
				clearTimeout(region.resizeTimeout);

			region.resizeTimeout=setTimeout(function(){region.doresize(region)},100);

			return true;
			
		};

		this.doresize=function(region) {
		
			var canvas=region.canvas;
			var area=$(region.area);

			if (!canvas) {
				console.log('martian: %o',region);
				return true;
			}

			var position=area.position();
			var width=area.width();
			var height=area.height();

			canvas.width=width;
			canvas.height=height;
			$(canvas).css({position: "absolute", top: position.top, left: position.left, width: width, height: height});
			region.draw(region.mode=='edit');

			if (canvas.child) {
				canvas.child.width=width;
				canvas.child.height=height;
				$(canvas.child).css({position: "absolute", top: position.top, left: position.left, width: width, height: height});
			};
		};
			
		this.mousedown=function(event) {

			if (event.button!=0) return true;

			if (!event.data) 
				var region=this;
			else
				var region=event.data.region;

			if (!region.callback('mousedown',event)) return false;

			$('#areas').focus();

			var canvas=region.canvas;

			var dotSize=region.settings.dotSize;
			var x=clip(event.pageX-$(canvas).offset().left,canvas.width);
			var y=clip(event.pageY-$(canvas).offset().top,canvas.height);

			if (region.mode=='edit' && !region.inRegion(x,y)) return true;

			var i;
			for (i=0; i>=0 && i<region.corners.length; ++i) {
				var orig=region.corners[i];
				if (Math.abs(x-orig.x)<dotSize/2 && Math.abs(y-orig.y)<dotSize/2) {
					switch(region.mode) {
						case 'design':
							if (i==0) {
								// click near first point: close shape
								region.close();
								return false;
							} else {
								// click near other point: ignore
								return false;
							};
							break;

						case 'edit':
							region.moving=i; // move corner
							region.moving_coords = {x: region.corners[region.moving].x, y: region.corners[region.moving].y}; // copy previous coords
							i=-2;
							break;
					}
				}
			};

			switch(region.mode) {

				case 'design':

					var ctx=canvas.ctx;
					ctx.clearRect(0,0,canvas.width,canvas.height);
					ctx.globalAlpha=1;
					ctx.fillStyle=region.settings.color;
					ctx.strokeStyle=region.settings.color;

					if (region.corners.length > 0 && event.shiftKey) {
						var last_point = region.corners.length - 1;
						var straight_coords = region.straightDrawingCoords(x, y, region.corners[last_point].x, region.corners[last_point].y);
						x = straight_coords.x;
						y = straight_coords.y;
					};

					region.corners.push({x: x, y: y});

					if (region.corners.length==1) {
					
						ctx.fillRect(x-dotSize/2,y-dotSize/2,dotSize,dotSize);
						ctx.stroke();

						$(document).bind('mousemove.region', {region: region}, region.mousemove);
						$(window).bind('keydown.region', {region: region}, region.keydown);

					} else {
						ctx.moveTo(x,y);

						for(var i=region.corners.length-1; i>=0; --i) {
							ctx.globalAlpha=region.settings.borderAlpha;
							ctx.lineTo(region.corners[i].x,region.corners[i].y);
							ctx.globalAlpha=1;
							ctx.fillRect(region.corners[i].x-dotSize/2,region.corners[i].y-dotSize/2,dotSize,dotSize);
						};
						ctx.stroke();

						canvas.child.ctx.clearRect(0,0,canvas.width,canvas.height);
					};
					break;

				case 'edit':

					if (i>0) { // click outside corner area

						// ignore click outside region
						if (!region.inRegion(x,y)) return true;

						// move all
						region.moving=-1;

						// save origin for move()
						region.corners1=new Array();
						for (i=0; i<region.corners.length; ++i) {
							region.corners1[i]={x: region.corners[i].x, y:region.corners[i].y};
						}
					};

					canvas.orig={x: x, y: y};
					$(document).bind('mousemove.region', {region: region}, region.mousemove);
					$(document).bind('mouseup.region', {region: region}, region.mouseup);
					break;
			};

			return false;
			
		};

		this.mousemove=function(event) {
	
			if (!event.data) 
				var region=this;
			else
				var region=event.data.region;

			if (!region.callback('mousemove',event)) return false;

			var canvas=region.canvas;

			var x=clip(event.pageX-$(canvas).offset().left,canvas.width);
			var y=clip(event.pageY-$(canvas).offset().top,canvas.height);

			switch(region.mode) {

				case 'design':

					var i=region.corners.length-1;
					var ctx=canvas.child.ctx;

					ctx.clearRect(0,0,canvas.width,canvas.height);
					ctx.beginPath();
					ctx.moveTo(region.corners[i].x,region.corners[i].y);
					if (event.shiftKey) {
						var straight_coords = region.straightDrawingCoords(x, y, region.corners[i].x, region.corners[i].y);
						x = straight_coords.x;
						y = straight_coords.y;
					};
					ctx.lineTo(x,y);
					ctx.closePath();
					ctx.stroke();

					break;

				case 'edit':

					region.move(x,y,false);
					region.draw(true);
				
					break;
			};

			return true;
		};

		this.mouseup=function(event) {

			if (!event.data) 
				var region=this;
			else
				var region=event.data.region;

			if (!region.callback('mouseup',event)) return false;

			var canvas=region.canvas;

			var x=clip(event.pageX-$(canvas).offset().left,canvas.width);
			var y=clip(event.pageY-$(canvas).offset().top,canvas.height);

			switch(region.mode) {

				case 'edit':

					region.move(x,y,false);
					region.draw(true);
					region.moving=-1;

					$(document).unbind('mousemove.region', region.mousemove);
					$(document).unbind('mouseup.region', region.mouseup);
				
					break;
			};

			return false;
		};

		this.move=function(x,y,relative) {

			var region=this;
			var canvas=region.canvas;

			if (region.moving>=0) {
				if (event.shiftKey) {
					var straight_coords = region.straightDrawingCoords(x, y, region.moving_coords.x, region.moving_coords.y);
					x = straight_coords.x;
					y = straight_coords.y;
				};
				region.corners[region.moving].x=x;
				region.corners[region.moving].y=y;

			} else {
				if (relative) {
					for (var i=0; i<region.corners.length; ++i) {
						region.corners[i].x+=x;
						region.corners[i].y+=y;
					}

				} else {
					var dx=x-canvas.orig.x;
					var dy=y-canvas.orig.y;

					if (event.shiftKey) {
						var straight_coords = region.straightDrawingCoords(dx, dy, 0, 0);
						dx = straight_coords.x;
						dy = straight_coords.y;
					};

					for (var i=0; i<region.corners.length; ++i) {
						region.corners[i].x=region.corners1[i].x+dx;
						region.corners[i].y=region.corners1[i].y+dy;
					}
				}
			}
		};

		this.draw=function(showCorners) {
			
			if (this.corners.length<3) return;	

			if (!this.callback('draw'))
				return;

			this.clear(this.canvas);

			var ctx=this.canvas.ctx;
			ctx.beginPath();
			ctx.globalAlpha=this.settings.borderAlpha;
			ctx.fillStyle=this.settings.color;
			ctx.strokeStyle=this.settings.color;

			var i=this.corners.length-1;
			ctx.moveTo(this.corners[i].x,this.corners[i].y);

			var dotSize=this.settings.dotSize;
			for (var i=0; i<this.corners.length; ++i) {
				var x=this.corners[i].x;
				var y=this.corners[i].y;
				ctx.lineTo(x,y);
				if (showCorners) {
					ctx.globalAlpha=1;
					ctx.fillRect(x-dotSize/2,y-dotSize/2,dotSize,dotSize);
					ctx.globalAlpha=this.settings.borderAlpha;
				}
			};

			ctx.stroke();
			ctx.closePath();

			ctx.globalAlpha=this.settings.regionAlpha;
			ctx.fill();
			ctx.stroke();

		};

		this.close=function(options) {
			var options=$.extend({force: false},options);
		
			var region=this;	
			var canvas=region.canvas;

			if (!this.callback('close'))
				return;

			switch(region.mode) {

				case 'design':
					if (!options.force && region.corners.length<3) return;	

					$(canvas.child).unbind('.region');
					$(document).unbind('.region');
					$(window).unbind('.region');

					canvas.child.parentNode.removeChild(canvas.child);
					canvas.child=undefined;

					if (region.corners.length<3) {
						canvas.parentNode.removeChild(canvas);
						region.canvas=undefined;
					} else {
						region.draw(false);
					};

					region.mode='closed';

					break;
	
			 	case 'edit':
					region.draw(false);
					region.mode='closed';
					$(document).unbind('.region');
					$(window).unbind('.region');
					break;
			};

			if (!this.callback('closed'))
				return;
		};

		this.keydown=function(event) {

			if (!event.data) 
				var region=this;
			else
				var region=event.data.region;

			if (!region.callback('keydown',event)) return false;

			switch(event.keyCode) {

				case 27: // escape
					region.reset();
					return false;

				case 13: // return
					switch(region.mode) {
						case 'design':
					 	case 'edit':
							region.close();
							return false;
					};
					break;

				case 46: // del
					switch(region.mode) {
						case 'design':
					 	case 'edit':
							region.corners.splice(0,region.corners.length);
							region.dispose();
							region.init();
							break;
					};
					break;

				case 37: // left
					switch(region.mode) {
					 	case 'edit':
							var offset=event.shiftKey?-10:-1;
							region.move(offset,0,true);
							region.draw(true);
							return false;
					};
					break;

				case 38: // top
					switch(region.mode) {
					 	case 'edit':
							var offset=event.shiftKey?-10:-1;
							region.move(0,offset,true);
							region.draw(true);
							return false;
					};
					break;

				case 39: // right
					switch(region.mode) {
					 	case 'edit':
							var offset=event.shiftKey?10:1;
							region.move(offset,0,true);
							region.draw(true);
							return false;
					};
					break;

				case 40: // bottom
					switch(region.mode) {
					 	case 'edit':
							var offset=event.shiftKey?10:1;
							region.move(0,offset,true);
							region.draw(true);
							return false;
					};
					break;

				default:
					if (console)
						console.log("keycode: %d",event.keyCode);
					break;
			};

			return true;
		};

		this.clear=function(canvas) {
			canvas.ctx.clearRect(0,0,canvas.width,canvas.height);
			canvas.ctx.beginPath();
			canvas.ctx.closePath();
			canvas.ctx.stroke();
		};

		this.reset=function() {

			if (!this.callback('reset')) return;

			switch(this.mode) {

				case 'design':
					this.clear(this.canvas);
					this.corners.splice(0,this.corners.length);

					this.clear(this.canvas.child);

					$(document).unbind('mousemove', this.mousemove);
					$(window).unbind('keydown', this.keydown);

					break;

				case 'edit':

					for (var i=0; i<this.corners.length; ++i) {
						this.corners[i]=this.corners0[i];
					};
						
					this.draw(false);
					this.mode='closed';

					$(document).unbind('mousedown', this.mousedown);
					$(document).unbind('mousemove', this.mousemove);
					$(document).unbind('mouseup', this.mouseup);
					$(window).unbind('keydown', this.keydown);

					break;
			}
		};

		this.canvas_dispose=function() {

			var canvas=this.canvas;
			if (!canvas) return false;

			canvas.parentNode.removeChild(canvas);

			if (canvas.child) {
				canvas.child.parentNode.removeChild(canvas.child);
				canvas.child=null;
			};
			this.canvas=null;

			return true;
		};

		this.dispose=function() {

			this.canvas_dispose();

			for (var i=0; document.region && i<document.region.length; ++i) {
				if (document.region[i].id==this.id) {
					document.region[i]=null;
					document.region.splice(i,1);
					break;
				}
			}
		};

		this.dblclick=function(event) {

			if (!event.data) 
				var region=this;
			else
				var region=event.data.region;

			if (!region.callback('dblclick',event)) return false;

			region.mousedown(event);

			if (region.mode=='design')
				region.close();

			return false;
		};

		// Region alpha must be !=0 so that inRegion works.
		// But style.opacity can be null,
		// or style.display can be 'none'.

		this.inRegion=function(x,y) { 
			if (x<0 || y<0 || x>=this.canvas.width || y>=this.canvas.height) return false;
			return (this.canvas.ctx.getImageData(x, y, 1, 1).data[3]!=0);
		};

		this.closeall=function() {
			$(document.region).each(function(i){
				if (document.region[i].mode!='closed') {
					document.region[i].close(true);
				}
			});
		};

		this.edit=function() {

			if (this.mode=='edit') 
				return false;

			var canvas=this.canvas;

			this.closeall();
			this.mode='edit';
			this.draw(true);

			// save origin for reset()
			this.corners0=new Array();
			for (var i=0; i<this.corners.length; ++i) {
				this.corners0[i]={x: this.corners[i].x, y: this.corners[i].y};
			};

			$(document).bind('mousedown.region', {region: this}, this.mousedown);
			$(window).bind('keydown.region', {region: this}, this.keydown);

			canvas.focus();

			return true;
			
		};

		this.toArea=function(htmlarea) {

			var coords=this.getCoords();

			if (!htmlarea) {
				htmlarea=document.createElement('area');
			};
			htmlarea.shape='poly';
			htmlarea.coords=coords;

			return htmlarea;
		};

		this.getCoords=function() {
			var coords='';
			for (var i=0; i<this.corners.length; ++i) {
				coords=coords+(i>0?',':'')+Math.floor(this.corners[i].x)+','+Math.floor(this.corners[i].y);
			};
			return coords;
		};

		this.fromArea=function(htmlarea) {
			this.setCorners(htmlarea.coords);
		};

		this.setCorners=function(coords) {
			var coords=eval('new Array('+coords+');');
			this.corners.splice(0,this.corners.length);
			for (var i=0; i<coords.length; i+=2) {
				this.corners[i/2]={x: coords[i], y: coords[i+1]};
			}
		};

		this.straightDrawingCoords=function(x, y, x_o, y_o) {
			var rewr_y =  (Math.abs(x-x_o) >= Math.abs(y-y_o)) && (Math.abs(x-x_o) > (this.settings.dotSize/2));
			var rewr_x = !(Math.abs(x-x_o) >= Math.abs(y-y_o)) && (Math.abs(y-y_o) > (this.settings.dotSize/2));
			return {x: rewr_x ? x_o : x, y: rewr_y ? y_o : y};
		};

		this.init();
		if (settings.coords) {
			this.setCorners(settings.coords);
			this.close();
		}

		$(window)
			.unbind('.regions')
			.bind('resize.regions', regions_resize)
			.bind('scroll.regions', regions_resize);

		this.resize(); // workaround for zero dimension canvas in webkit

		return this;
		
	};

	// region.onmousedown can be function or string.
	// Region is set to edit mode and can be moved when
	// function returns true, or inconditionnaly after evaluating string
	
	$(document).bind('mousedown.region',function (event) {

		var region_list=new Array;

		$('canvas').each(function(){

			if (!this.region) 
				return;

			var offset=$(this).offset();
			if (this.region && this.region.inRegion(event.pageX-offset.left,event.pageY-offset.top)) 
				region_list.push(this.region);

		});

		if (region_list.length) {

			var max=0;
			var maxi=0;

			for (var i=0; i<region_list.length; ++i) {
				if (region_list[i].canvas.style.zIndex>max) {
					max=region_list[i].canvas.style.zIndex;
					maxi=i;
				}
			};

			for (var i=maxi; i>=0; --i) {

				var doedit=true;

				if (typeof(region_list[maxi].settings.onmousedown)=='function') {
					doedit=region_list[maxi].settings.onmousedown(event);
				} else {
					eval(region_list[maxi].settings.onmousedown);
				};

				if (doedit && region_list[maxi].edit()) {
					region_list[maxi].mousedown(event);
					return false;
				}
			}
		}

		return true;
	});

	$.fn.newRegion=function(options) {

		var settings = $.extend({
			canvasOffset: 9, /* WTF?.. */
			coords: undefined,
			dotSize: 8,
			color: 'blue',
			regionAlpha: 0.1,
			borderAlpha: 0.3,
			corners: new Array,
			className: 'region',
			callback: function(){return true;},
			onmousedown: '' /* region editable when empty script, or function returning true */
		}, options);

		$('canvas').each(function() {
			if (this.region)
				this.region.close(true);
		});

		this.each(function() {
			if (!document.region)
				document.region=new Array;
			document.region.push(new region(this,settings));
		});

		return this;
	};

	function regions_resize() {
		$(document.region).each(function(idx,region) {
			region.resize();
		});
	}

}) (jQuery);

