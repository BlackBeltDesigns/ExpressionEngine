/*!
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2016, EllisLab, Inc.
 * @license		https://expressionengine.com/license
 * @link		http://ellislab.com
 * @since		Version 2.0
 * @filesource
 */
/* This file exposes three callback functions:
 *
 * EE.manager.showPrefsRow and EE.manager.hidePrefsRow and
 * EE.manager.refreshPrefs
 */

/*jslint browser: true, onevar: true, undef: true, nomen: true, eqeqeq: true, plusplus: false, bitwise: true, regexp: false, strict: true, newcap: true, immed: true */

/*global $, jQuery, EE, window, document, console, alert */

"use strict";

(function ($) {
	$(document).ready(function () {
		$('table .toolbar .view a').click(function (e) {
			var modal = $(this).attr('rel');
			$.ajax({
				type: "GET",
				url: EE.file_view_url.replace('###', $(this).data('file-id')),
				dataType: 'html',
				success: function (data) {
					$("." + modal + " div.box").html(data);
				}
			})
		});
	});
})(jQuery);