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

$(document).ready(function () {
	// Auto-assign category parents if configured to do so
	if (EE.category.auto_assign_parents == 'y') {
		$('body').on('click', 'input[name^="categories"]:checkbox', function(){

			// If we're unchecking, make sure its children are also unchecked
			if ( ! $(this).is(':checked')) {
				$(this).parents('li')
					.first()
					.find('input:checkbox')
					.prop('checked', false)
					.trigger('change');
			}

			// If we're checking, check its parents too
			if ($(this).is(':checked')) {
				$(this).parents('li')
					.find('> label input:checkbox')
					.prop('checked', true)
					.trigger('change');
			}
		});
	}

	// Category management tools toggle
	$('body').on('click', '.toggle-tools a.toggle-btn', function (e) {
		e.preventDefault();
		var cat_container = $(this).parents('.nestable'),
			turning_on = $(this).hasClass('off'),
			list_reorder = $('.list-reorder', cat_container),
			toolbar = $('.toolbar', cat_container);

		$(this).toggleClass('off', ! turning_on);
		$(this).toggleClass('on', turning_on);
		$('.form-ctrls button').toggleClass('disable', turning_on);
		$('input[type=checkbox]', cat_container).prop('disabled', turning_on);

		// On
		if (turning_on) {
			list_reorder.clearQueue().animate({
				'margin-left': '-10px'
			}, 400);
			toolbar.stop().fadeIn();
		} else { // Off\
			list_reorder.clearQueue().animate({
				'margin-left': '-50px'
			}, 400);
			toolbar.stop().fadeOut();
		}
	});

	// Category modal
	$('body').on('click', 'a[rel=modal-checkboxes-edit]', function (e) {
		var modal_link = $(this),
			modal_name = modal_link.attr('rel'),
			modal = $('.' + modal_name),
			isEditing = modal_link.parents('fieldset').find('a.toggle-btn').hasClass('on'),
			category_form_url,
			category_form_url_qs = $(this).data('groupId');

		// If we're in an editing state, be sure to return to an editing state
		if (isEditing) {
			category_form_url_qs = category_form_url_qs + '/editing';
		}

		category_form_url = EE.category.add.URL.replace('###', category_form_url_qs)

		// Clear out modal from last request
		$('div.box', modal).html('<p align="center"><a class="btn work" href="">Loading</a></p>');

		// Are we editing a category? Create a different form URL
		if ($(this).data('contentId')) {
			category_form_url = EE.category.edit.URL.replace('###', $(this).data('groupId') + '/' + $(this).data('contentId'));
		}

		$.ajax({
			type: "GET",
			url: category_form_url,
			dataType: 'html',
			success: function (data) {
				load_category_modal_data(modal, data, modal_link);

				// Bind ee_url_title for new categories only
				if (modal_link.data('contentId') === undefined) {
					$('input[name=cat_name]', modal).bind('keyup keydown', function() {
						$(this).ee_url_title('input[name=cat_url_title]');
					});
				}
			}
		})
	});

	// Category deletion
	$('body').on('click', 'a[rel=modal-checkboxes-confirm-remove]', function (e) {
		var modal = $('.' + $(this).attr('rel')),
			modal_link = $(this);

		// Add the name of the category we're deleting to the modal
		$('.checklist', modal)
			.html('')
			.append('<li>' + $(this).data('confirm') + '</li>');
		// Set the category ID to send to the categories deletion handler
		$('input[name="categories[]"]', modal).val($(this).data('contentId'));

		$('form', modal).off('submit').on('submit', function() {

			$.ajax({
				type: 'POST',
				url: this.action,
				data: $(this).serialize(),
				dataType: 'json',
				success: function(result) {
					if (result.messageType == 'success') {
						modal.trigger('modal:close');

						load_new_category_field(modal_link, result.body);
					}
				}
			});

			return false;
		});

		e.preventDefault();
	});

	// Load the category edit/delete modal and bind the submit handler
	function load_category_modal_data(modal, data, modal_link) {
		$('div.box', modal).html(data);

		EE.cp.formValidation.init(modal);

		$('form', modal).off('submit').on('submit', function() {

			$.ajax({
				type: 'POST',
				url: this.action,
				data: $(this).add('input[name="categories[]"]').serialize()+'&save_modal=yes',
				dataType: 'json',

				success: function(result) {
					if (result.messageType == 'success') {
						modal.trigger('modal:close');

						load_new_category_field(modal_link, result.body);
					} else {
						load_category_modal_data(modal, result.body, modal_link);
					}
				}
			});

			return false;
		});
	}

	// Load a new category field and bind necessary things
	function load_new_category_field(modal_link, field_body)
	{
		var field = modal_link.parents('fieldset').find('.setting-field'),
			checked_cats = get_checked_categories(modal_link.data('groupId'));

		field.html(field_body);
		bind_nestable($('.nestable', field));

		// Restore checked categories
		check_categories(modal_link.data('groupId'), checked_cats);

		EE.cp.addLastToChecklists();
	}

	// Returns an array of selected category IDs in a given category group
	function get_checked_categories(group_id) {
		var categories = $('div[data-nestable-group="'+group_id+'"] ul.nested-list li input[type="checkbox"]:checked');

		return $.map(categories, function(cat) {
			return $(cat).val();
		});
	}

	// Given an array of category IDs, checks those categories
	function check_categories(group_id, cat_ids) {
		$.each(cat_ids, function(i, cat_id) {
			$('div[data-nestable-group="'+group_id+'"] input[value="'+cat_id+'"]')
				.prop('checked', true)
				.trigger('change');
		});
	}

	// Initial binding on page load
	bind_nestable();

	// Binds our Nestable plugin to a given element, or .nestable if none specified
	function bind_nestable(root) {
		var root = root || '.nestable';

		$(root).nestable({
			listNodeName: 'ul',
			listClass: 'nestable-list',
			itemClass: 'nestable-item',
			rootClass: 'nestable',
			dragClass: 'drag-tbl-row.nested-list',
			handleClass: 'list-reorder',
			placeElement: $('<li><div class="tbl-row drag-placeholder"><div class="none"></div></div></li>'),
			expandBtnHTML: '',
			collapseBtnHTML: '',
			maxDepth: 10,
			constrainToRoot: true
		}).on('change', function() {

			EE.cp.addLastToChecklists();

			$.ajax({
				url: EE.category.reorder.URL.replace('###', $(this).data('nestable-group')),
				data: {'order': $(this).nestable('serialize') },
				type: 'POST',
				dataType: 'json'
			});
		});
	}

});
