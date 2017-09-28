/*!
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2016, EllisLab, Inc.
 * @license		https://expressionengine.com/license
 * @link		https://ellislab.com
 * @since		Version 4.0.0
 * @filesource
 */

"use strict";

(function ($) {
	$(document).ready(function () {
		// Disable inputs
		$('.fluid-field-templates :input').attr('disabled', 'disabled');

	    var addField = function(e) {
			var fluidBlock   = $(this).closest('.fluid-wrap'),
			    fieldToAdd   = $(this).data('field-name'),
			    fieldCount   = fluidBlock.data('field-count'),
			    fieldToClone = $('.fluid-field-templates .fluid-item[data-field-name="' + fieldToAdd + '"]'),
			    fieldClone   = fieldToClone.clone();

			fieldCount++;

			fieldClone.html(
				fieldClone.html().replace(
					RegExp('new_field_[0-9]{1,}', 'g'),
					'new_field_' + fieldCount
				)
			);

			fluidBlock.data('field-count', fieldCount);

			// Enable inputs
			fieldClone.find(':input').removeAttr('disabled');

			// Bind the "add" button
			fieldClone.find('a[data-field-name]').click(addField);

			// Insert it
			if ( ! $(this).parents('.fluid-item').length) {
				// the button at the bottom of the form was used.
				$('.fluid-actions', fluidBlock).before(fieldClone);
			} else {
				$(this).closest('.fluid-item').after(fieldClone);
			}

			// Bind the new field's inputs to AJAX form validation
			if (EE.cp && EE.cp.formValidation !== undefined) {
				EE.cp.formValidation.bindInputs(fieldClone);
			}

			e.preventDefault();
			fluidBlock.find('.open').trigger('click');

			$(fluidBlock).trigger('fluidBlock:addField', [fieldClone, fieldToClone]);
	    };

		$('a[data-field-name]').click(addField);

		$('.fluid-wrap').on('click', 'a.fluid-remove', function(e) {
			$(this).closest('.fluid-item').remove();
			e.preventDefault();
		});

		$('.fluid-wrap').sortable({
			axis: 'y',						// Only allow horizontal dragging
			containment: 'parent',			// Contain to parent
			handle: 'span.reorder',			// Set drag handle to the top box
			items: '.fluid-item',			// Only allow these to be sortable
			sort: EE.sortable_sort_helper	// Custom sort handler
		})
	});
})(jQuery);
