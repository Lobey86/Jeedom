
(function($) {
	$.notice = {
		show: function(message) {
			/** Configuration */
			var top = 15; /* px */
			var showDuration = 3000;
			var fadeoutDuration = 1000;
			
			/** Launch the notification */
			$('html, body').animate({scrollTop:0});
			$('<div></div>').attr('id', 'notice').html(message).appendTo('body').centerX().css('top', (0+top)+'px');
			
			/** Switch off the notification */
			setTimeout(function() {$('#notice').animate({ opacity: 0, top: '-20px' }, fadeoutDuration);}, showDuration);
			setTimeout(function() {$('#notice').remove();}, showDuration + fadeoutDuration);
		}
	};
	
	jQnotice = function(message) { $.notice.show(message); };
})(jQuery);

jQuery.fn.center = function () {
	this.centerX();
	this.centerY();
	return this;
};


jQuery.fn.centerY = function () {
	this.css("position","absolute");
	this.css("top", ( window.innerHeight - this.height() ) / 2+$(window).scrollTop() + "px");
	return this;
};

jQuery.fn.centerX = function () {
	this.css("position","absolute");
	this.css("left", ( window.innerWidth - this.width() ) / 2+$(window).scrollLeft() + "px");
	return this;
};
