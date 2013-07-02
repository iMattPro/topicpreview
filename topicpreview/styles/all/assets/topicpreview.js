;(function ( $, window, document, undefined ) {

	$.fn.topicPreview = function( options ) {

		var settings = $.extend( {
			"width" : 300,
			"delay" : 500,
			"left"  : 35,
			"top"   : 40
		}, options );

		var previewTimeout = 0,
			previewContainer = $('<div id="topicPreview" class="topicPreview"></div>').appendTo("body");

		return this.each(function() {
			var obj = $(this),
				previewText = obj.closest("li").find(".topic_preview_first").text() || obj.closest("tr").find(".topic_preview_first").text(), // cache topic preview text
				originalTitle = obj.closest("dt").attr("title"); // cache original title attributes

			obj.hover(function() {
				// Proceed only if there is content to display
				if (previewText === undefined || previewText === '') {
					return false;
				}

				// clear any existing timeouts
				if (previewTimeout !== 0) {
					clearTimeout(previewTimeout);
				}

				// remove original titles to prevent overlap
				obj.attr("title", "").closest("dt").attr("title", "");

				previewTimeout = setTimeout(function() {
					// clear the timeout var after delay and function begins to execute	
					previewTimeout = 0;

					// Fill the topicPreview
					$("#topicPreview").html(previewText);

					// Display the topicPreview positioned relative to the hover object
					previewContainer
						.css({
							"max-width" : settings.width + "px",
							"top"   : obj.offset().top + settings.top + "px",
							"left"  : obj.offset().left + settings.left + "px"
						})
						.fadeIn("fast") // display the topicPreview with a fadein and some animation
						.animate({'top': '-=15px'}, {duration: 'fast', queue: false}, function() {
							// animation complete
						}); 
				}, settings.delay); // Use a delay before showing in topicPreview

			}, function() {
				// clear any existing timeouts
				if (previewTimeout !== 0) {
					clearTimeout(previewTimeout);
				}

				// Remove topicPreview
				previewContainer.stop(true, true).fadeOut("fast"); // hide the topicPreview with a fadeout
				obj.closest("dt").attr("title", originalTitle); // reinstate original title attributes
			});
		});
	};

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
// 		});
// 	};

	// Run on DOM ready
	$(function() {
		$(".topictitle").topicPreview();
	});

})( jQuery, window, document );
