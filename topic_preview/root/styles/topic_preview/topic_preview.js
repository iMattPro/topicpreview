/*
 * jQuery ToolTips for Topic Preview
 * https://github.com/VSEphpbb/topic_preview
 *
 * Copyright 2013, Matt Friedman
 * Licensed under the GPL Version 2 license.
 * http://www.opensource.org/licenses/GPL-2.0
 */

;(function ( $, window, document, undefined ) {

	$.fn.topicPreview = function( options ) {

		var settings = $.extend( {
			"style" : "light",
			"delay" : 1500,
			"width" : 320,
			"left"  : 75,
			"top"   : 25,
			"drift" : 15
		}, options );

		var previewTimeout = 0,
			previewContainer = $('<div id="topic_preview" class="' + settings.style + '"></div>').css("width", settings.width).appendTo("body");

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
					previewContainer
						.html(content)
						.find(".topic_preview_text_first")
						.append(firstPostText);

					// Window bottom edge detection, invert topic preview if needed 
					var previewTop = obj.offset().top + settings.top,
						previewBottom = previewTop + previewContainer.height() + 8;
					previewContainer.toggleClass("invert", edgeDetect(previewBottom));
					previewTop = edgeDetect(previewBottom) ? obj.offset().top - previewContainer.outerHeight() - 8 : previewTop;

					// Display the topic_preview positioned relative to the hover object
					previewContainer
						.css({
							"top"   : previewTop + "px",
							"left"  : obj.offset().left + settings.left + "px"
						})
						.fadeIn("fast"); // display the topic preview with a fadein
				}, settings.delay); // Use a delay before showing in topic_preview

			}, function() {
				// clear any existing timeouts
				if (previewTimeout !== 0) {
					clearTimeout(previewTimeout);
				}

				// Remove topic_preview
				previewContainer.stop(true, true).fadeOut("fast") // hide the topic_preview with a fadeout
					.animate({"top": "-=" + settings.drift + "px"}, {duration: "fast", queue: false}, function() {
						// animation complete
					});
			});
		});
	};

	// Check if y coord extends beyond bottom edge of window (with 100px of pad)
	function edgeDetect(y) {
		return ( y >= ($(window).scrollTop() + $(window).height() - 100) );
	}

})( jQuery, window, document );
