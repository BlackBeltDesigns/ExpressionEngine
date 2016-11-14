/*!
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2016, EllisLab, Inc.
 * @license		https://expressionengine.com/license
 * @link		https://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

"use strict";

(function ($) {
	$(document).ready(function () {
		// Single Relationship:
		//   When the radio button is clicked, copy the chosen data into the
		//   div.relate-wrap-chosen area
		$('div.publish').on('click', '.relate-wrap input:radio', function (e) {
			var relationship = $(this).closest('.relate-wrap');
			var label = $(this).closest('label');
			var chosen = $(this).closest('.scroll-wrap')
				.data('template')
				.replace(/{entry-id}/g, $(this).val())
				.replace(/{entry-title}/g, label.data('entry-title'))
				.replace(/{channel-title}/g, label.data('channel-title'));

			relationship.find('.relate-wrap-chosen .no-results')
				.closest('label')
				.hide()
				.removeClass('block');
			relationship.find('.relate-wrap-chosen .relate-manage').remove();
			relationship.find('.relate-wrap-chosen').first().append(chosen);
			relationship.removeClass('empty');
		});

		// Multiple Relationships
		//   When checkbox is clicked, copy the chosen data into the second
		//   div.relate-wrap div.scroll-wrap area
		$('div.publish').on('click', '.relate-wrap input:checkbox', function (e) {
			var relationship = $(this).closest('.relate-wrap')
				.siblings('.relate-wrap')
				.first();

			var label = $(this).closest('label');

			// jQuery will decode encoded HTML in a data attribute,
			// so we'll use this trick to keep it encoded
			var encoded_title = $('<div/>').text(label.data('entry-title')).html();

			var chosen = $(this).closest('.scroll-wrap')
				.data('template')
			.replace(/{entry-id}/g, $(this).val())
			.replace(/{entry-title}/g, encoded_title)
			.replace(/{channel-title}/g, label.data('channel-title'));

			// If the checkbox was unchecked run the remove event
			if ($(this).prop('checked') == false) {
				relationship.find('.scroll-wrap a[data-entry-id=' + $(this).val() + ']').click();
				return;
			}

			relationship.find('.scroll-wrap .no-results').hide();
			relationship.removeClass('empty');
			relationship.find('.scroll-wrap').first().append(chosen);
			relationship.find('.scroll-wrap label')
				.last()
				.data('entry-title', encoded_title)
				.data('channel-id', label.data('channel-id'))
				.data('channel-title', label.data('channel-title'))
				.prepend('<span class="relate-reorder"></span>');

			$(this).siblings('input:hidden')
				.val(relationship.find('.scroll-wrap label').length);
		});

		// Removing Relationships
		$('div.publish').on('click', '.relate-wrap .relate-manage a', function (e) {
			var choices = $(this).closest('.relate-wrap');
			var chosen = $(this).closest('.relate-wrap');

			// Is this a multiple relationship?
			if (choices.hasClass('w-8')) {
				choices = choices.siblings('.relate-wrap').first();
			}
			else
			{
				choices.addClass('empty');
			}

			choices.find('.scroll-wrap :checked[value=' + $(this).data('entry-id') + ']')
				.attr('checked', false)
				.parents('.choice')
				.removeClass('chosen')
				.find('input:hidden')
				.val(0);

			choices.find('.scroll-wrap input[type="hidden"][value=' + $(this).data('entry-id') + ']')
				.remove();

			$(this).closest('label').remove();

			if (chosen.find('.relate-manage').length == 0) {
				if (chosen.hasClass('w-8')) {
					chosen.addClass('empty')
						.find('.no-results')
						.show();
				} else {
					chosen.find('.relate-wrap-chosen .no-results')
						.closest('label')
						.show()
						.removeClass('hidden')
						.addClass('block');
				}
			}

			e.preventDefault();
		});

		var ajaxTimer,
			ajaxRequest;

		function ajaxRefresh(elem, search, channelId, delay) {
			var settings = $(elem).closest('.relate-wrap').data('settings');

			settings['search'] = search;
			settings['channel_id'] = channelId;

			// Cancel the last AJAX request
			clearTimeout(ajaxTimer);
			if (ajaxRequest) {
				ajaxRequest.abort();
			}

			ajaxTimer = setTimeout(function() {
				ajaxRequest = $.ajax({
					url: EE.relationship.filter_url,
					data: $.param(settings),
					type: 'POST',
					dataType: 'json',
					success: function(ret) {
						console.log(ret);
					}
				});
			}, delay);

		}

		// Filter by Channel
		$('div.publish').on('click', '.relate-wrap .relate-actions .filters a[data-channel-id]', function (e) {
			var search = $(this).closest('.relate-wrap').find('.relate-search').val();

			ajaxRefresh(this, search, $(this).data('channel-id'), 0);

			$(document).click(); // Trigger the code to close the menu
			e.preventDefault();
		});

		// Search Relationships
		$('div.publish').on('interact', '.relate-wrap .relate-actions .relate-search', function (e) {
			var channelId = $(this).closest('.relate-actions')
				.find('.filters .has-sub .faded')
				.data('channel-id');

			ajaxRefresh(this, $(this).val(), channelId, 150);
		});

		// Sortable!
		var sortable_options = {
			axis: 'y',
			cursor: 'move',
			handle: '.relate-reorder',
			items: 'label',
		};

		$('.w-8.relate-wrap .scroll-wrap').sortable(sortable_options);
	});
})(jQuery);
