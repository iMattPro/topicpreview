/*
 * jQuery ToolTips for Topic Preview
 * https://github.com/VSEphpbb/topic_preview
 *
 * Copyright 2012, Matt Friedman
 * Licensed under the GPL Version 2 license.
 * http://www.opensource.org/licenses/GPL-2.0
 */

;(function ( $, window, document, undefined ) {

	$.fn.topicPreview = function( options ) {

		var settings = $.extend( {
			"style" : "light",
			"width" : 320,
			"delay" : 500,
			"left"  : 0,
			"top"   : 12,
			"drift" : 12
		}, options );

		var previewContainer = $('<div id="topic_preview" class="' + settings.style + '"><div id="topic_preview_inner"></div><div id="topic_preview_pointer"><div id="topic_preview_pointer_inner"></div></div></div>').appendTo("body");
		var previewTimeout = 0;

		return this.each(function() {

			var obj = $(this),
				hoverObject = obj.parent().find(".topictitle"),
				firstPostText = obj.attr("title"); // cache title attributes

			// remove default titles
			obj.attr("title", "");

			hoverObject.hover(function() {

				// Proceed only if there is content to display
				var content = $("#topic_preview_" + obj.attr("id")).html();
				if (content === undefined || content === '') {
					return false;
				}

				// clear any existing timeouts
				if (previewTimeout !== 0) {
					clearTimeout(previewTimeout);
				}

				previewTimeout = setTimeout(function() {

					// clear the timeout var after delay and function begins to execute	
					previewTimeout = 0;
	
					// Fill the topic preview
					$("#topic_preview_inner")
						.html(content)
						.find(".topic_preview_text_first > span")
						.html(firstPostText);

					// Handle window top edge detection, and invert topic preview if needed 
					var previewTop = obj.offset().top - previewContainer.height() - settings.top + settings.drift;
					$("#topic_preview_pointer, #topic_preview_pointer_inner").toggleClass("invert", topEdgeDetect(previewTop));
					previewTop = topEdgeDetect(previewTop) ? obj.offset().top + (settings.top * 3) + settings.drift: previewTop;

					// position the topic preview relative to the hover object
					previewContainer
						.css({
							"width" : settings.width,
							"top"   : (previewTop + "px"),
							"left"  : ((obj.offset().left + settings.left) + "px")
						})
						.fadeIn("fast") // display the topic preview with a fadein
						.animate({'top': "-=" + settings.drift + "px"}, {duration: "fast", queue: false}, function() {
							// animation complete
						});
				}, settings.delay); // Use a delay before fading in topic preview

			}, function() {

				if (previewTimeout !== 0) {
					clearTimeout(previewTimeout); // clear any existing timeouts
				}

				previewContainer.stop(true, true).fadeOut("fast") // hide the topic preview with a fadeout
					.animate({'top': "-=" + settings.drift + "px"}, {duration: "fast", queue: false}, function() {
						// animation complete
					});
			});
		});
	};

	// Check if y coord extends beyond top edge of window
	function topEdgeDetect(y) {
		return ( y <= $(window).scrollTop() );
	}

})( jQuery, window, document );
