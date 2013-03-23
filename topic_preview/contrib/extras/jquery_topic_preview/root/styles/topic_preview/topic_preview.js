/*
 * jQuery ToolTips for Topic Preview
 * https://github.com/VSEphpbb/topic_preview
 *
 * Copyright 2012, Matt Friedman
 * Licensed under the GPL Version 2 license.
 * http://www.opensource.org/licenses/GPL-2.0
 */

;(function ( $, window, document, undefined ) {

	$.fn.tooltips = function( options ) {

		var settings = $.extend( {
			"style" : "light",
			"width" : 320,
			"delay" : 500,
			"leftX" : 0,
			"topY"  : 12
		}, options );

		var tooltipContainer = $('<div id="tooltip" class="' + settings.style + '"><div id="tooltip_inner"></div><div id="tooltip_pointer"><div id="tooltip_pointer_inner"></div></div></div>').appendTo("body");
		var tipTimeout = 0;

		return this.each(function() {

			var obj = $(this);
			var firstPostText = obj.attr("title"); // cache title attributes

			obj.hover(function() {

				// Proceed only if there is content to display
				var content = $("#topic_preview_" + obj.attr("id")).html();
				if (content === undefined || content === '') {
					return false;
				}

				// clear any existing timeouts
				if (tipTimeout !== 0) {
					clearTimeout(tipTimeout);
				}

				// remove default title
				obj.attr("title", "");

				tipTimeout = setTimeout(function() {

					// clear the timeout var after delay and function begins to execute	
					tipTimeout = 0;
	
					// Fill the tooltip
					$("#tooltip_inner")
						.html(content)
						.find(".tooltip_text_first > span")
						.html(firstPostText);

					// Handle window top edge detection, and invert tooltip if needed 
					var tooltipTop = obj.offset().top - tooltipContainer.height() - settings.topY;
					$("#tooltip_pointer, #tooltip_pointer_inner").toggleClass("invert", topEdgeDetect(tooltipTop));
					tooltipTop = topEdgeDetect(tooltipTop) ? obj.offset().top + (settings.topY * 3) : tooltipTop;

					// position the tooltip relative to the hover object
					tooltipContainer
						.css({
							"width" : settings.width,
							"top"   : (tooltipTop + "px"),
							"left"  : ((obj.offset().left + settings.leftX) + "px")
						})
						.fadeIn("fast"); // display the tooltip with a fadein
				}, settings.delay); // Use a delay before fading in tooltip

			}, function() {

				if (tipTimeout !== 0) {
					clearTimeout(tipTimeout); // clear any existing timeouts
				}

				tooltipContainer.stop(true, true).fadeOut("fast"); // hide the tooltip with a fadeout

			});

		});

	};

	// Check if y coord extends beyond top edge of window
	function topEdgeDetect(y) {
		return ( y <= $(window).scrollTop() );
	}

})( jQuery, window, document );
