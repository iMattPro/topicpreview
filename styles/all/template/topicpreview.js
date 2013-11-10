/*
 * jQuery ToolTips for Topic Preview
 * https://github.com/VSEphpbb/topic_preview
 *
 * Copyright 2013, Matt Friedman
 * Licensed under the GPL Version 2 license.
 * http://www.opensource.org/licenses/GPL-2.0
 */

;(function ($, window, document, undefined) {

	$.fn.topicPreview = function (options) {

		var settings = $.extend({
			dir: "ltr",
			delay: 1500,
			width: 360,
			left: 35,
			top: 25,
			drift: 15
		}, options),
			previewTimeout = 0,
			previewContainer = $('<div id="topic_preview"></div>').css("width", settings.width).appendTo("body");

		// Do not allow delay times less than 300ms to prevent tooltip madness
		settings.delay = Math.max(settings.delay, 300);

		// Add rtl class for right-to-left languages to avatar images
		$(".topic_preview_avatar").toggleClass("rtl", (settings.dir === "rtl")).children("img").one("error", function () {
			// Replace any broken/missing avatar images in topic previews
			$(this).attr("src", settings.noavatar);
		});

		return this.each(function () {
			var obj = $(this),
				originalTitle = obj.closest("dt").attr("title"); // cache original title attributes

			obj.hover(function () {
				// Grab tooltip content
				var content = obj.closest("li").find(".topic_preview_content").html() || obj.closest("tr").find(".topic_preview_content").html();

				// Proceed only if there is content to display
				if (content === undefined || content === '') {
					return false;
				}

				// clear any existing timeouts
				if (previewTimeout !== 0) {
					clearTimeout(previewTimeout);
				}

				// remove original titles to prevent overlap
				obj.attr("title", "").closest("dt").attr("title", "");

				previewTimeout = setTimeout(function () {
					// clear the timeout var after delay and function begins to execute	
					previewTimeout = 0;

					// Fill the topic preview
					previewContainer.html(content);

					// Window bottom edge detection, invert topic preview if needed 
					var previewTop = obj.offset().top + settings.top,
						previewBottom = previewTop + previewContainer.height() + 8;
					previewContainer.toggleClass("invert", edgeDetect(previewBottom));
					previewTop = edgeDetect(previewBottom) ? obj.offset().top - previewContainer.outerHeight(true) - 8 : previewTop;

					// Display the topic preview positioned relative to the hover object
					previewContainer
						.stop(true, true) // stop any running animations first
						.css({
							"top": previewTop + "px",
							"left": obj.offset().left + settings.left + (settings.dir === "rtl" ? (obj.width() - previewContainer.width()) : 0) + "px"
						})
						.fadeIn("fast"); // display the topic preview with a fadein
				}, settings.delay); // Use a delay before showing in topic preview

			}, function () {
				// clear any existing timeouts
				if (previewTimeout !== 0) {
					clearTimeout(previewTimeout);
				}

				// Remove topic preview
				previewContainer
					.stop(true, true) // stop any running animations first
					.fadeOut("fast") // hide the topic preview with a fadeout
					.animate({"top": "-=" + settings.drift + "px"}, {duration: "fast", queue: false}, function () {
						// animation complete
					});
				obj.closest("dt").attr("title", originalTitle); // reinstate original title attributes
			}).on("click", function () {
				// Remove topic preview immediately on click as failsafe
				previewContainer.hide();
				clearTimeout(previewTimeout); // Safari 7 bug, clear all timeouts
			});
		});
	};

	// Check if y coord is within 100 pixels of bottom edge of browser window
	function edgeDetect(y) {
		return (y >= ($(window).scrollTop() + $(window).height() - 100));
	}

})(jQuery, window, document);
