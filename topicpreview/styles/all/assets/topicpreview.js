;(function ( $, window, document, undefined ) {

	$.fn.topicPreview = function( options ) {

		var settings = $.extend( {
			"width" : 300,
			"delay" : 500,
			"left"  : 35,
			"top"   : 40
		}, options );

		var tipTimeout = 0,
			tooltipContainer = $('<div id="tooltip" class="tooltip"></div>').appendTo("body");

		return this.each(function() {
			var obj = $(this),
				firstPostText = obj.closest("li").find(".topic_preview_first").text() || obj.closest("tr").find(".topic_preview_first").text(), // cache topic preview text
				originalTitle = obj.closest("dt").attr("title"); // cache original title attributes

			obj.hover(function() {
				// Proceed only if there is content to display
				if (firstPostText === undefined || firstPostText === '') {
					return false;
				}

				// clear any existing timeouts
				if (tipTimeout !== 0) {
					clearTimeout(tipTimeout);
				}

				// remove original titles to prevent overlap
				obj.attr("title", "").closest("dt").attr("title", "");

				tipTimeout = setTimeout(function() {
					// clear the timeout var after delay and function begins to execute	
					tipTimeout = 0;

					// Fill the tooltip
					$("#tooltip").html(firstPostText);

					// Display the tooltip positioned relative to the hover object
					tooltipContainer
						.css({
							"max-width" : settings.width + "px",
							"top"   : obj.offset().top + settings.top + "px",
							"left"  : obj.offset().left + settings.left + "px"
						})
						.fadeIn("fast") // display the tooltip with a fadein and some animation
						.animate({'top': '-=15px'}, {duration: 'fast', queue: false}, function() {
							// animation complete
						}); 
				}, settings.delay); // Use a delay before showing in tooltip

			}, function() {
				// clear any existing timeouts
				if (tipTimeout !== 0) {
					clearTimeout(tipTimeout);
				}

				// Remove tooltip
				tooltipContainer.stop(true, true).fadeOut("fast"); // hide the tooltip with a fadeout
				obj.closest("dt").attr("title", originalTitle); // reinstate original title attributes
			});
		});
	};

	// Run on DOM ready
	$(function() {
		$(".topictitle").topicPreview();
	});

})( jQuery, window, document );

// (function( $, document ){
// 
// 	$.fn.topicPreview = function() {  
// 
// 		return this.each(function() {
// 
// 			var container = $(this).closest("li") /*$(this).closest("tr")*/,
// 				firstPostText = container.find(".topic_preview_first").text(),
// 				lastPostText = container.find(".topic_preview_last").text();
// 
// 			$(this).attr("title", firstPostText);
// 
// 			container.find(".icon_topic_latest").attr("title", lastPostText);
// 
// 		});
// 
// 	};
// 
// 	$(document).ready(function() {
// 
// 		$(".topictitle").topicPreview();
// 
// 	});
// 
// })( jQuery, document );
