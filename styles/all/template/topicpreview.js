/**
 * jQuery ToolTips for Topic Preview
 * https://github.com/iMattPro/topic_preview
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
			hideTimeout,
			previewContainer = $('<div id="topic_preview" class="topic_preview_container"></div>').css('width', settings.width).appendTo('body');

		// Do not allow delay times less than 300 ms to prevent tooltip madness
		settings.delay = Math.max(settings.delay, 300);

		$('.topic_preview_avatar')
			// Add rtl class for right-to-left languages to avatar images
			.toggleClass('rtl', (settings.dir === 'rtl'))
			.children('img')
			.brokenImage({})
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
				clearTimeout(previewTimeout);
				previewTimeout = undefined;
			}
			if (hideTimeout) {
				clearTimeout(hideTimeout);
				hideTimeout = undefined;
			}

			// remove original titles to prevent overlap
			obj.removeAttr('title')
				.clearTitles('dt')
				.clearTitles('dl')
			;

			previewTimeout = setTimeout(function() {
				// clear the timeout var after delay and function begins to execute
				previewTimeout = undefined;

				// Fill the topic preview with scrollable content
				previewContainer.html('<div class="topic_preview_scrollable">' + content + '</div>');

				// Pointer offset
				var pointerOffset = 8;

				// Window top-edge detection, invert topic preview if needed
				var previewTop = obj.offset().top - previewContainer.outerHeight(true) - pointerOffset;
				previewContainer.toggleClass('invert', !topEdgeDetect(previewTop));
				previewTop = topEdgeDetect(previewTop) ? previewTop : obj.offset().top + settings.position.top;

				// Display the topic preview positioned relative to the hover object
				previewContainer
					.stop(true, true) // stop any running animations first
					.css({
						top: previewTop + 'px',
						left: obj.offset().left + settings.position.left + (settings.dir === 'rtl' ? (obj.width() - previewContainer.width()) : 0) + 'px'
					})
					.fadeIn('fast') // display the topic preview with a fadein
				;

				// Add hover handlers to the preview container to keep it visible
				previewContainer
					.off('mouseenter mouseleave') // Remove any existing handlers
					.on('mouseenter', function() {
						if (hideTimeout) {
							clearTimeout(hideTimeout);
							hideTimeout = undefined;
						}
					})
					.on('mouseleave', function() {
						hideTopicPreview.call(obj);
					})
				;
			}, settings.delay); // Use a delay before showing in topic preview
		};

		// Hide the topic preview tooltip
		var hideTopicPreview = function() {
			var obj = $(this);

			// clear any existing timeouts
			if (previewTimeout) {
				clearTimeout(previewTimeout);
				previewTimeout = undefined;
			}

			// Add a small delay before hiding to allow mouse to move to tooltip
			hideTimeout = setTimeout(function() {
				hideTimeout = undefined;

				// Remove topic preview
				previewContainer
					.stop(true, true) // stop any running animations first
					.fadeOut('fast') // hide the topic preview with a fadeout
					.animate({
						top: '-=' + settings.drift + 'px'
					}, {
						duration: 'fast',
						queue: false,
						complete: function() {
							// animation complete
						}
					})
				;
				obj.restoreTitles('dt').restoreTitles('dl'); // reinstate original title attributes
			}, 100); // Small delay to allow mouse movement to tooltip
		};

		// Check if y coordinate is within 50 pixels of the bottom edge of a browser window
		// var bottomEdgeDetect = function(y) {
		// 	return (y >= ($(window).scrollTop() + $(window).height() - 50));
		// };

		// Check if y coordinate is within 50 pixels of the top edge of a browser window
		var topEdgeDetect = function(y) {
			return (y >= ($(window).scrollTop() + 50));
		};

		return this.each(function() {
			$(this)
				.on('mouseenter', showTopicPreview)
				.on('mouseleave', hideTopicPreview)
				.on('click', function() {
					// Remove the topic preview immediately on click as failsafe
					previewContainer.hide();
					// clear any existing timeouts
					if (previewTimeout) {
						clearTimeout(previewTimeout);
						previewTimeout = undefined;
					}
					if (hideTimeout) {
						clearTimeout(hideTimeout);
						hideTimeout = undefined;
					}
				})
			;
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

				$(image).on('error', function() {
					insertPlaceholder();
				});

				setTimeout(function() {
					// Check if the image failed to load with fallback for older browsers
					var isIncomplete = image.complete !== undefined ? !image.complete : false;
					var hasNoHeight = image.naturalHeight !== undefined ? image.naturalHeight === 0 : image.height === 0;
					if (isIncomplete || hasNoHeight) {
						insertPlaceholder();
					}
				}, options.timeout);

				function insertPlaceholder() {
					$(image).replaceWith('<div class="topic_preview_no_avatar"></div>');
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
