;(function ( $, window, document, undefined ) {

	$.fn.topicPreview = function( options ) {

		var settings = $.extend( {
			"delay" : 1500,
			"width" : 300,
			"left"  : 35,
			"top"   : 25,
			"drift" : 15
		}, options );

		var previewTimeout = 0,
			previewContainer = $('<div id="topicPreview"></div>').css("width", settings.width).appendTo("body");

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
					previewContainer.html(previewText);

					// Display the topicPreview positioned relative to the hover object
					previewContainer
						.css({
							"top"   : obj.offset().top + settings.top + settings.drift + "px",
							"left"  : obj.offset().left + settings.left + "px"
						})
						.fadeIn("fast") // display the topicPreview with a fadein and some animation
						.animate({"top": "-=" + settings.drift + "px"}, {duration: 'fast', queue: false}, function() {
							// animation complete
						});
				}, settings.delay); // Use a delay before showing in topicPreview

			}, function() {
				// clear any existing timeouts
				if (previewTimeout !== 0) {
					clearTimeout(previewTimeout);
				}

				// Remove topicPreview
				previewContainer.stop(true, true).fadeOut("fast") // hide the topicPreview with a fadeout
					.animate({"top": "-=" + settings.drift + "px"}, {duration: "fast", queue: false}, function() {
						// animation complete
					});
				obj.closest("dt").attr("title", originalTitle); // reinstate original title attributes
			});
		});
	};

})( jQuery, window, document );
