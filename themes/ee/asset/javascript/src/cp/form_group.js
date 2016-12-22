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

// fields that are children of hidden parent fields
var hidden = {
	"always-hidden": false
};

// real visibility states of hidden children
var states = {
	"always-hidden": false
};


$(document).ready(function() {

	var fields = $('*[data-group-toggle]:radio');

	toggleInputs(fields, '', false);

	// loop through all of the toggles and record their current state
	// we need this so that we can check if a section's visiblity should
	// override the visibility of a child.
	$('*[data-group-toggle]').each(function(index, el) {

		if ($(this).is(':radio') && ! $(this).is(':checked')) {
			return;
		}

		var config = $(this).data('groupToggle'),
			value  = $(this).val();

		$.each(config, function (key, data) {
			if (states[data] == undefined || states[data] == false) {
				states[data] = !!(key == value);
			}
		});
	});

	// next go through and trigger our toggle on each to get the
	// correct initial states. this cannot be combined with the
	// above loop.
	$('*[data-group-toggle]').each(function(index, el) {

		if ($(this).is(':radio') && ! $(this).is(':checked')) {
			return;
		}

		EE.cp.form_group_toggle(this);

		var config = $(this).data('groupToggle');

		// Initially, if there are radio buttons across multiple groups
		// that share the same name, only the last one specified to be
		// checked will be checked, so we need to prefix those inputs
		// in form_group_toggle and then tell the browser to populate
		// the radio buttons with their default checked state
		/*
		$.each(config, function (key, data) {
			var elements = $('*[data-group="'+data+'"]');

			elements.find(':radio').each(function() {
				$(this).prop('checked', $(this).attr('checked') == 'checked');
			});
		});
		*/
	});
});

function toggleFields(fields, show, key) {
	toggleInputs(fields, key, show);
	fields.toggle(show);

	fields.each(function(i, field) {
		var fieldset = $(field).closest('fieldset');

		if (fieldset.hasClass('invalid')) {
			if (fieldset.find('input:visible').not('input.btn').size() == 0) {
				fieldset.removeClass('invalid');
				fieldset.find('em.ee-form-error-message').remove();
			}
		}
	});
}

function toggleSections(sections, show, key) {
	sections.each(function() {
		$(this).toggle(show);
		$(this).nextUntil('h2, .form-ctrls').each(function() {

			var field = $(this),
				group = field.data('group');

			// if we're showing this section, but the field is hidden
			// from another toggle, then don't show it
			if (group) {
				hidden[group] = ! show;
			}

			if (show && group) {
				toggleFields(field, states[group], key);
			} else {
				toggleFields(field, show, key);
			}
		});
	});
}

EE.cp.form_group_toggle = function(element) {

	var config = $(element).data('groupToggle'),
		value  = $(element).val();

	states = {
		"always-hidden": false
	};

	// Show the selected group and enable its inputs
	$.each(config, function (key, data) {
		var field_targets = $('*[data-group="'+data+'"]');
		var section_targets = $('*[data-section-group="'+data+'"]');

		if (states[data] == undefined || states[data] == false) {
			states[data] = (key == value);
		}
		toggleFields(field_targets, hidden[data] ? false : (key == value));
		toggleSections(section_targets, states[data]);
	});

	// The reset the form .last values
	var form = $(element).closest('form');

	form.find('fieldset.last').not('.grid-wrap fieldset').removeClass('last');
	form.find('h2, .form-ctrls').each(function() {
		$(this).prevAll('fieldset:visible').first().addClass('last');
	});
}

// This all kind of came about from needing to preserve radio button
// state for radio buttons but identical names across various groups.
// In an effort not to need to prefix those input names, we'll handle
// it automatically with this function.
function toggleInputs(container, group_name, enable) {
	//return;
	container.find(':radio').each(function() {

//		var input = $(this),
//			name = input.attr('name'),
//			clean_name = (name) ? name.replace('el_disabled_'+group_name+'_', '') : '';

		var input = $(this);

		// Disable inputs that aren't shown, we don't need those in POST
		input.attr('disabled', ! enable);

		var state = input.data('el_checked');

		if ( ! state) {
			state = ($(this).attr('checked') == 'checked');
			input.data('el_checked', state);

			input.change(function() {
				input.data('el_checked', input.prop('checked'));
			});
		}

		if (enable) {
			input.prop('checked', state);
		}
		/*
		// Prefixing the name ensures radio buttons will keep their state
		// when changing the visible group, as well as any JS handlers
		// based on name should take note of and inputs that are no
		// longer in their scope
		if (name) {
			if (enable) {
				input.attr('name', clean_name);
			} else {
				input.attr('name', 'el_disabled_'+group_name+'_'+clean_name);
			}
		}
		*/
	});
}

})(jQuery);
