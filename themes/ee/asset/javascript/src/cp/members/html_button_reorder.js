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

(function($) {

"use strict";

$(document).ready(function() {

	$('table').eeTableReorder({
		afterSort: function(row) {
			$.ajax({
				url: EE.html_buttons.reorder_url,
				data: {'order': $('input[name="order[]"]').serialize() },
				type: 'POST',
				dataType: 'json',
				error: function(xhr, text, error) {
					// Let the user know something went wrong
					if ($('body > .banner').size() == 0) {
						$('body').prepend(EE.alert.reorder_ajax_fail);
					}
				}
			});
		}
	});

});

})(jQuery);
