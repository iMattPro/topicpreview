/**
 * jQuery ToolTips for Topic Preview
 * https://github.com/VSEphpbb/topic_preview
 *
 * Copyright 2013, Matt Friedman
 * Licensed under the GPL Version 2 license.
 * http://www.opensource.org/licenses/GPL-2.0
 */

(function($) { // Avoid conflicts with other libraries

	'use strict';

	$.fn.topicPreview = function(options) {

		var settings = $.extend({
				dir: 'ltr',
				delay: 1000,
				width: 360,
				drift: 15,
				position: { left: 35, top: 25 }
			}, options),
			previewTimeout,
			previewContainer = $('<div id="topic_preview"></div>').css('width', settings.width).appendTo('body');

		// Do not allow delay times less than 300ms to prevent tooltip madness
		settings.delay = Math.max(settings.delay, 300);

		$('.topic_preview_avatar')
			// Add rtl class for right-to-left languages to avatar images
			.toggleClass('rtl', (settings.dir === 'rtl'))
			.children('img')
			.brokenImage({ replacement: settings.noavatar })
		;

		// Display the topic preview tooltip
		var showTopicPreview = function() {
			var obj = $(this);

			// Grab tooltip content
			var content = obj.closest('li, tr').find('.topic_preview_content').html();

			// Proceed only if there is content to display
			if (content === undefined || content === '') {
				return false;
			}

			// clear any existing timeouts
			if (previewTimeout) {
				previewTimeout = clearTimeout(previewTimeout);
			}

			// remove original titles to prevent overlap
			obj.removeAttr('title')
				.clearTitles('dt')
				.clearTitles('dl')
			;

			previewTimeout = setTimeout(function() {
				// clear the timeout var after delay and function begins to execute
				previewTimeout = undefined;

				// Fill the topic preview
				previewContainer.html(content);

				// Pointer offset
				var pointerOffset = 8;

				// Window bottom edge detection, invert topic preview if needed
				var previewTop = obj.offset().top + settings.position.top,
					previewBottom = previewTop + previewContainer.height() + pointerOffset;
				previewContainer.toggleClass('invert', edgeDetect(previewBottom));
				previewTop = edgeDetect(previewBottom) ? obj.offset().top - previewContainer.outerHeight(true) - pointerOffset : previewTop;

				// Display the topic preview positioned relative to the hover object
				previewContainer
					.stop(true, true) // stop any running animations first
					.css({
						top: previewTop + 'px',
						left: obj.offset().left + settings.position.left + (settings.dir === 'rtl' ? (obj.width() - previewContainer.width()) : 0) + 'px'
					})
					.fadeIn('fast') // display the topic preview with a fadein
				;
			}, settings.delay); // Use a delay before showing in topic preview
		};

		// Hide the topic preview tooltip
		var hideTopicPreview = function() {
			var obj = $(this);

			// clear any existing timeouts
			if (previewTimeout) {
				previewTimeout = clearTimeout(previewTimeout);
			}

			// Remove topic preview
			previewContainer
				.stop(true, true) // stop any running animations first
				.fadeOut('fast') // hide the topic preview with a fadeout
				.animate({ top: '-=' + settings.drift + 'px' }, { duration: 'fast', queue: false }, function() {
					// animation complete
				})
			;
			obj.restoreTitles('dt').restoreTitles('dl'); // reinstate original title attributes
		};

		// Check if y coord is within 50 pixels of bottom edge of browser window
		var edgeDetect = function(y) {
			return (y >= ($(window).scrollTop() + $(window).height() - 50));
		};

		return this.each(function() {
			$(this).hover(showTopicPreview, hideTopicPreview).on('click', function() {
				// Remove topic preview immediately on click as failsafe
				previewContainer.hide();
				// clear any existing timeouts
				if (previewTimeout) {
					previewTimeout = clearTimeout(previewTimeout);
				}
			});
		});
	};

	/*
	 * https://github.com/alexrabarts/jquery-brokenimage
	 * Licensed under the MIT: http://www.opensource.org/licenses/mit-license.php
	 */
	$.extend($.fn, {
		brokenImage: function(options) {
			var defaults = {
				timeout: 3000
			};

			options = $.extend(defaults, options);

			return this.each(function() {
				// Replace the image with a placeholder if:
				// a. loading fails with an error event or;
				// b. loading takes longer than timeout
				var image = this;

				$(image).bind('error', function() {
					insertPlaceholder();
				});

				setTimeout(function() {
					var test = new Image(); // Virgin image with no styles to affect dimensions
					test.src = image.src;

					if (test.height === 0) {
						insertPlaceholder();
					}
				}, options.timeout);

				function insertPlaceholder() {
					if (options.replacement) {
						image.src = options.replacement;
					} else {
						$(image).css('visibility', 'hidden');
					}
				}
			});
		},
		clearTitles: function(el) {
			return this.each(function() {
				var $obj = $(this).closest(el);
				var title = $obj.attr('title');
				if (typeof title !== typeof undefined && title !== false) {
					$obj.data('title', title).removeAttr('title');
				}
			});
		},
		restoreTitles: function(el) {
			return this.each(function() {
				var $obj = $(this).closest(el);
				$obj.attr('title', $obj.data('title'));
			});
		}
	});

})(jQuery); // Avoid conflicts with other libraries
