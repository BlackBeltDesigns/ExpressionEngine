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
		function setupFileField(container) {
			$('.file-field-filepicker', container).FilePicker({
				callback: function(data, references) {
					var input = references.input_value;

					// Close the modal
					references.modal.find('.m-close').click();

					// Assign the value {filedir_#}filename.ext
					input.val('{filedir_' + data.upload_location_id + '}' + data.file_name);

					// Set the thumbnail
					references.input_img.attr('src', data.thumb_path);

					// Show the figure
					input.siblings('figure').show();

					// Hide the upload button
					input.siblings('p.solo-btn').hide();

					// Hide the "missing file" error
					input.siblings('em').hide();
				}
			});

			$('li.remove a').click(function (e) {
				var figure = $(this).closest('figure');
				figure.hide();
				figure.siblings('em').hide(); // Hide the "missing file" erorr
				figure.siblings('input[type="hidden"]').val('');
				figure.siblings('p.solo-btn').show();
				e.preventDefault();
			});
		}

		setupFileField();

		Grid.bind('file', 'display', function(cell) {
			var button = $('.file-field-filepicker', cell),
				input = $('input[type="hidden"]', cell),
				safe_name = input.attr('name').replace(/[\[\]']+/g, '_');

			button.attr('data-input-value', input.attr('name'));
			button.attr('data-input-image', safe_name);

			$('.file-chosen img', cell).attr('id', safe_name);

			setupFileField(cell);
		});
	});
})(jQuery);
