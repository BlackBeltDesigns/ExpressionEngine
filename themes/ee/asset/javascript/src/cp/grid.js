(function($) {

/**
 * Grid Namespace
 */
var Grid = window.Grid = {

	// Event handlers stored here, direct access outside only from
	// Grid.Publish class
	_eventHandlers: [],

	/**
	 * Binds an event to a fieldtype
	 *
	 * Available events are:
	 * 'display' - When a row is displayed
	 * 'remove' - When a row is deleted
	 * 'beforeSort' - Before sort starts
	 * 'afterSort' - After sort ends
	 * 'displaySettings' - When settings form is displayed
	 *
	 * @param	{string}	fieldtypeName	Class name of fieldtype so the
	 *				correct cell object can be passed to the handler
	 * @param	{string}	action			Name of action
	 * @param	{func}		func			Callback function for event
	 */
	bind: function(fieldtypeName, action, func) {
		if (this._eventHandlers[action] == undefined) {
			this._eventHandlers[action] = [];
		}

		// Each fieldtype gets one method per handler
		this._eventHandlers[action][fieldtypeName] = func;
	}
};

/**
 * Grid Publish class
 *
 * @param	{string}	field		Selector of table to instantiate as a Grid
 */
Grid.Publish = function(field, settings) {
	if (field === null || field === undefined) {
		return;
	}
	this.root = $(field);
	this.blankRow = $('tr.grid-blank-row', this.root);
	this.emptyField = $('tr.no-results', this.root);
	this.tableActions = $('tr.tbl-action', this.root);
	this.rowContainer = this.root.children('tbody');
	this.settings = (settings !== undefined) ? settings : EE.grid_field_settings[field.id];
	this.init();

	this.eventHandlers = [];
}

Grid.Publish.prototype = {

	init: function() {
		this._bindSortable();
		this._bindAddButton();
		this._bindDeleteButton();
		this._toggleRowManipulationButtons();
		this._fieldDisplay();

		// Store the original row count so we can properly increment new
		// row placeholder IDs in _addRow()
		this.original_row_count = this._getRows().size();

		// Disable input elements in our blank template container so they
		// don't get submitted on form submission
		this.blankRow.find(':input').attr('disabled', 'disabled');
	},

	/**
	 * Allows rows to be reordered
	 */
	_bindSortable: function() {
		var that = this;

		this.root.eeTableReorder({
			// Fire 'beforeSort' event on sort start
			beforeSort: function(row) {
				that._fireEvent('beforeSort', row);
			},
			// Fire 'afterSort' event on sort stop
			afterSort: function(row) {
				that._fireEvent('afterSort', row);
			}
		});
	},

	/**
	 * Adds rows to a Grid field based on the fields minimum rows setting
	 * and how many rows already exist
	 */
	_addMinimumRows: function() {
		// Figure out how many rows we need to add
		var rowsCount = this._getRows().size(),
			neededRows = this.settings.grid_min_rows - rowsCount;

		// Show empty field message if field is empty and no rows are needed
		if (rowsCount == 0 && neededRows == 0) {
			this.emptyField.show();
		}

		// Add the needed rows
		while (neededRows > 0) {
			this._addRow();

			neededRows--;
		}
	},

	/**
	 * Toggles the visibility of the Add button and Delete buttons for rows
	 * based on the number of rows present and the max and min rows settings
	 */
	_toggleRowManipulationButtons: function() {
		var rowCount = this._getRows().size(),
			addButton = this.root.parents('.grid-publish').find('.toolbar .add a').parents('ul.toolbar'),
			reorderCol = this.root.find('th.reorder-col'),
			gridRemove = this.root.find('th.grid-remove');

		// Show add button below field when there are more than zero rows
		addButton.toggle(rowCount > 0);

		if (rowCount > 0) {
			// Only show reorder header if table is configured to be reorderable
			if (reorderCol.size() == 0 && $('td.reorder-col', this.root).size() > 0) {
				$('> thead tr', this.root).prepend(
					$('<th/>', { class: 'first reorder-col' })
				);
			}
			if (gridRemove.size() == 0) {
				$('> thead tr', this.root).append(
					$('<th/>', { class: 'last grid-remove' })
				);
			}
		} else {
			reorderCol.remove();
			gridRemove.remove();
		}

		if (this.settings.grid_max_rows !== '') {
			// Show add button if row count is below the max rows setting,
			// and only if there are already other rows present
			addButton.toggle(rowCount < this.settings.grid_max_rows && rowCount > 0);
		}

		if (this.settings.grid_min_rows !== '') {
			var deleteButtons = this.root.find('.toolbar .remove');

			// Show delete buttons if the row count is above the min rows setting
			deleteButtons.toggle(rowCount > this.settings.grid_min_rows);
		}

		// Do not allow sortable to run when there is only one row, otherwise
		// the row becomes detached from the table and column headers change
		// width in a fluid-column-width table
		this.rowContainer.find('td.reorder-col').toggleClass('sort-cancel', rowCount == 1);
	},

	/**
	 * Returns current number of data rows in the Grid field, makes sure
	 * to skip counting of blank row, empty row and header row
	 *
	 * @return	{int}	Number of rows
	 */
	_getRows: function() {
		return this.rowContainer.children('tr').not(this.blankRow.add(this.emptyField).add(this.tableActions));
	},

	/**
	 * Binds click listener to Add button to insert a new row at the bottom
	 * of the field
	 */
	_bindAddButton: function() {
		var that = this;

		this.root.parents('.grid-publish')
			.find('.toolbar .add a')
			.add('.no-results .btn', this.root)
			.add('.tbl-action .btn.add', this.root)
			.on('click', function(event) {
				event.preventDefault();

				that._addRow();
			}
		);
	},

	/**
	 * Inserts new row at the bottom of our field
	 */
	_addRow: function() {
		// Clone our blank row
		el = this.blankRow.clone();

		el.removeClass('grid-blank-row');
		el.removeClass('hidden');
		el.show();

		// Increment namespacing on inputs
		this.original_row_count++;
		el.html(
			el.html().replace(
				RegExp('new_row_[0-9]{1,}', 'g'),
				'new_row_' + this.original_row_count
			)
		);

		// Add the new row ID to the field data
		$('> td', el).attr('data-new-row-id', 'new_row_' + this.original_row_count);

		// Enable inputs
		el.find(':input').removeAttr('disabled');

		// Append the row to the end of the row container
		if (this.tableActions.length) {
			this.tableActions.before(el);
		} else {
			this.rowContainer.append(el);
		}

		// Make sure empty field message is hidden
		this.emptyField.hide();

		// Hide/show delete buttons depending on minimum row setting
		this._toggleRowManipulationButtons();

		// Fire 'display' event for the new row
		this._fireEvent('display', el);

		$(this.root).trigger('grid:addRow', el);

		// Bind the new row's inputs to AJAX form validation
		if (EE.cp && EE.cp.formValidation !== undefined) {
			EE.cp.formValidation.bindInputs(el);
		}
	},

	/**
	 * Binds click listener to Delete button in row column to delete the row
	 */
	_bindDeleteButton: function() {
		var that = this;

		this.root.on('click', 'td:last-child .toolbar .remove a', function(event) {
			event.preventDefault();

			row = $(this).parents('tr');

			// Fire 'remove' event for this row
			that._fireEvent('remove', row);

			// Remove the row
			row.remove();

			that._toggleRowManipulationButtons();

			// Show our empty field message if we have no rows left
			if (that._getRows().size() == 0) {
				that.emptyField.show();
			}
		});
	},

	/**
	 * Called after main initialization to fire the 'display' event
	 * on pre-exising rows
	 */
	_fieldDisplay: function() {
		var that = this;

		setTimeout(function(){
			that._getRows().each(function() {
				that._fireEvent('display', $(this));
			});

			that._addMinimumRows();
		}, 500);
	},

	/**
	 * Fires event to fieldtype callbacks
	 *
	 * @param	{string}		action	Action name
	 * @param	{jQuery object}	row		jQuery object of affected row
	 */
	_fireEvent: function(action, row) {
		// If no events regsitered, don't bother
		if (Grid._eventHandlers[action] === undefined) {
			return;
		}

		// For each fieldtype binded to this action
		for (var fieldtype in Grid._eventHandlers[action]) {
			// Find the sepecic cell(s) for this fieldtype and send each
			// to the fieldtype's event hander
			row.find('td[data-fieldtype="'+fieldtype+'"]').each(function() {
				Grid._eventHandlers[action][fieldtype]($(this));
			});
		}
	}
};

/**
 * Grid Settings class
 */
Grid.Settings = function(settings) {
	this.root = $('.grid-wrap');
	this.settingsScroller = this.root.find('.grid-clip');
	this.settingsContainer = this.root.find('.grid-clip-inner');
	this.colTemplateContainer = $('#grid_col_settings_elements');
	this.blankColumn = this.colTemplateContainer.find('.grid-item');
	this.settings = settings;

	this.init();
}

Grid.Settings.prototype = {

	init: function() {
		this._bindResize();
		this._bindSortable();
		this._bindActionButtons(this.root);
		this._toggleDeleteButtons();
		this._bindColTypeChange();
		this._bindValidationCallback();

		// If this is a new field, bind the automatic column title plugin
		// to the first column
		this._bindAutoColName(this.root.find('div.grid-item[data-field-name^="new_"]'));

		// Fire displaySettings event
		this._settingsDisplay();

		// Disable input elements in our blank template container so they
		// don't get submitted on form submission
		this.colTemplateContainer.find(':input').attr('disabled', 'disabled');
	},

	/**
	 * Since the Grid settings form is laid out differently than most forms, we
	 * need to do some extra DOM handling when a Grid settings field is validated
	 */
	_bindValidationCallback: function() {
		EE.cp.formValidation.bindCallbackForField('grid', function(result, error, field) {
			var alert = $('div.grid-wrap').prev(),
				fieldName = field.attr('name'),
				columnId = '['+field.parents('.grid-item').attr('data-field-name')+']';

			// Get the last segment of the fieldname so we don't show duplicate errors
			// if multiple columns have the same error; instead we'll keep track of
			// the columns that have the error and keep it persisting while those errors
			// still exist
			if (fieldName.indexOf('[') > -1) {
				fieldName = fieldName.substr(-(fieldName.length - fieldName.lastIndexOf('[')));
			}

			// Isolate the error text from the <em>
			var errorText = $('<div/>').html(error).contents().html(),
				existingError;

			// If validation failed, get the error element based on current error text
			if (error !== undefined) {
				existingError = $('span[data-field="'+fieldName+'"]:contains("'+errorText+'")', alert);
			// If validation passed, get the error element associated with our column and field
			} else {
				existingError = $('span[data-field="'+fieldName+'"][data-columns*="'+columnId+'"]', alert);
			}

			// Get the error element for this field and error if we've already created it
			var existingErrorColumns = existingError.attr('data-columns');

			// On validation failure
			if (result === false) {
				// Remove the inline error set by the form validation JS library,
				// it doesn't work with the design here
				field.parents('fieldset').find('em.ee-form-error-message').remove();

				// Create a span for the error message with some data attributes
				// that we'll use later to see if we need to remove the message
				// or make sure there are no duplicate error messages
				var errorSpan = $('<span/>').attr({
							'data-field': fieldName,
							'data-columns': columnId,
							'style': 'display: block'
						}).text(errorText);

				// Alert not already there? Add it and add our error message
				if ( ! alert.hasClass('alert')) {
					var alert = $('<div/>').html(EE.alert.grid_error).contents();
					alert.html('<p>'+errorSpan.prop('outerHTML')+'</p>');
					alert.insertBefore($('div.grid-wrap'));
				// Alert already exists
				} else {
					// There isn't an error span for this error yet, add it anew
					if (existingError.size() == 0) {
						$('p', alert).append(errorSpan);
					// Otherwise, there's already an error for this, keep track of
					// which columns have this error
					} else {
						if (existingErrorColumns.indexOf(columnId) == -1) {
							existingError.attr('data-columns', existingErrorColumns + columnId);
						}
					}
				}
			// Validation succeeded? Sweet, remove the error
			} else if (alert.hasClass('alert')) {

				// If the error exists, we need to remove the column ID of the column
				// that validated successfully for this field, and if the error doesn't
				// exist for any more columns, remove the error entirely
				if (existingError.size() > 0) {
					existingError.attr('data-columns', existingErrorColumns.replace(columnId, ''));
					if (existingError.attr('data-columns') == '') {
						existingError.remove();
					}
				}
				// No more errors? Get rid of the alert
				if ($('span', alert).size() == 0) {
					alert.remove();
				}
			}
		});
	},

	/**
	 * Upon page load, we need to resize the column container to fit the number
	 * of columns we have
	 */
	_bindResize: function() {
		var that = this;

		$(document).ready(function() {
			that._resizeColContainer();
		});
	},

	/**
	 * Resizes column container based on how many columns it contains
	 *
	 * @param	{boolean}	animated	Whether or not to animate the resize
	 */
	_resizeColContainer: function(animated) {
		this.settingsContainer.animate( {
			width: this._getColumnsWidth()
		},
		(animated == true) ? 400 : 0);
	},

	/**
	 * Calculates total width the columns in the container should take up,
	 * plus a little padding for the Add button
	 *
	 * @return	{int}	Calculated width
	 */
	_getColumnsWidth: function() {
		var columns = this.root.find('.grid-item');

		// Actual width of column is width + 32
		return columns.size() * (columns.width() + 32);
	},

	/**
	 * Allows columns to be reordered
	 */
	_bindSortable: function() {
		this.settingsContainer.sortable({
			axis: 'x',						// Only allow horizontal dragging
			containment: 'parent',			// Contain to parent
			handle: 'li.reorder',			// Set drag handle to the top box
			items: '.grid-item',			// Only allow these to be sortable
			sort: EE.sortable_sort_helper	// Custom sort handler
		});
		this.settingsContainer.find('li.reorder a').on('click', function(e){
			e.preventDefault();
		});
	},

	/**
	 * Convenience method for binding column manipulation buttons (add, copy, remove)
	 * for a given context
	 *
	 * @param	{jQuery Object}	context		Object to find action buttons in to bind
	 */
	_bindActionButtons: function(context) {
		this._bindAddButton(context);
		this._bindCopyButton(context);
		this._bindDeleteButton(context);
	},

	/**
	 * Binds click listener to Add button to insert a new column at the end
	 * of the columns
	 *
	 * @param	{jQuery Object}	context		Object to find action buttons in to bind
	 */
	_bindAddButton: function(context) {
		var that = this;

		context.find('.grid-tools li.add a').on('click', function(event) {
			event.preventDefault();

			var parentCol = $(this).parents('.grid-item');

			that._insertColumn(that._buildNewColumn(), parentCol);
		});
	},

	/**
	 * Binds click listener to Copy button in each column to clone the column
	 * and insert it after the column being cloned
	 *
	 * @param	{jQuery Object}	context		Object to find action buttons in to bind
	 */
	_bindCopyButton: function(context) {
		var that = this;

		context.find('.grid-tools li.copy a').off('click').on('click', function(event) {
			event.preventDefault();

			var parentCol = $(this).parents('.grid-item');

			that._insertColumn(
				// Build new column based on current column
				that._buildNewColumn(parentCol),
				// Insert AFTER current column
				parentCol
			);
		});
	},

	/**
	 * Binds click listener to Delete button in each column to delete the column
	 *
	 * @param	{jQuery Object}	context		Object to find action buttons in to bind
	 */
	_bindDeleteButton: function(context) {
		var that = this;

		context.on('click', '.grid-tools li.remove a', function(event) {
			event.preventDefault();

			var settings = $(this).parents('.grid-item');

			// Only animate column deletion if we're not deleting the last column
			if (settings.index() == $('.grid-item:last', that.root).index()) {
				settings.remove();
				that._resizeColContainer(true);
				that._toggleDeleteButtons();
			} else {
				settings.animate({
					opacity: 0
				}, 200, function() {
					// Clear HTML before resize animation so contents don't
					// push down bottom of column container while resizing
					settings.html('');

					settings.animate({
						width: 0
					}, 200, function() {
						settings.remove();
						that._resizeColContainer(true);
						that._toggleDeleteButtons();
					});
				});
			}
		});
	},

	/**
	 * Looks at current column count, and if there are multiple columns,
	 * shows the delete buttons; otherwise, hides delete buttons if there is
	 * only one column
	 */
	_toggleDeleteButtons: function() {
		var colCount = this.root.find('.grid-item').size(),
			deleteButtons = this.root.find('.grid-tools li.remove');

		deleteButtons.toggle(colCount > 1);
	},

	/**
	 * Inserts a new column after a specified element
	 *
	 * @param	{jQuery Object}	column		Column to insert
	 * @param	{jQuery Object}	insertAfter	Element to insert the column
	 *				after; if left blank, defaults to last column
	 */
	_insertColumn: function(column, insertAfter) {
		var lastColumn = $('.grid-item:last', this.root);

		// Default to inserting after the last column
		if (insertAfter == undefined) {
			insertAfter = lastColumn;
		}

		// If we're inserting a column in the middle of other columns,
		// animate the insertion so it's clear where the new column is
		if (insertAfter.index() != lastColumn.index()) {
			column.css({ opacity: 0 })
		}

		column.insertAfter(insertAfter);

		this._resizeColContainer();
		this._toggleDeleteButtons();

		// If we are inserting a column after the last column, scroll to
		// the end of the column container
		if (insertAfter.index() == lastColumn.index()) {
			// Scroll container to the very end
			this.settingsScroller.animate({
				scrollLeft: this._getColumnsWidth()
			}, 700);
		}

		column.animate({
			opacity: 1
		}, 400);

		// Bind automatic column name
		this._bindAutoColName(column);

		// Bind column manipulation buttons
		this._bindActionButtons(column);

		// Bind AJAX form validation
		EE.cp.formValidation.bindInputs(column);

		// Fire displaySettings event
		this._fireEvent('displaySettings', $('.grid-col-settings-custom > div', column));
	},

	/**
	 * Binds ee_url_title plugin to column label box to auto-populate the
	 * column name field; this is only applied to new columns
	 *
	 * @param	{jQuery Object}	el	Column to bind ee_url_title to
	 */
	_bindAutoColName: function(columns) {
		columns.each(function(index, column) {
			$('input.grid_col_field_label', column).bind("keyup keydown", function() {
				$(this).ee_url_title($(column).find('input.grid_col_field_name'), true);
			});
		});
	},

	/**
	 * Builts new column from scratch or based on an existing column
	 *
	 * @param	{jQuery Object}	el	Column to base new column off of, when
	 *				copying an existing column for example; if left blank,
	 *				defaults to blank column
	 * @return	{jQuery Object}	New column element
	 */
	_buildNewColumn: function(el) {
		if (el == undefined) {
			el = this.blankColumn.clone();
		} else {
			// Clone our example column
			el = this._cloneWithFormValues(el);
		}

		// Clear out column name field in new column because it has to be unique
		el.find('input[name$="\\[col_name\\]"]').attr('value', '');

		// Need to make sure the new column's field names are unique
		var new_namespace = 'new_' + $('.grid-item', this.root).size();
		var old_namespace = el.data('field-name');

		el.html(
			el.html().replace(
				RegExp('name="grid\\[cols\\]\\[' + old_namespace + '\\]', 'g'),
				'name="grid[cols][' + new_namespace + ']'
			)
		);

		el.attr('data-field-name', new_namespace);

		// Make sure inputs are enabled if creating blank column
		el.find(':input').removeAttr('disabled').removeClass('grid_settings_error');

		return el;
	},

	/**
	 * Binds change listener to the data type columns dropdowns of each column
	 * so we can load the correct settings form for the selected fieldtype
	 */
	_bindColTypeChange: function() {
		var that = this;

		this.root.on('change', 'select.grid_col_select', function(event) {
			// New, fresh settings form
			var settings = that.colTemplateContainer
				.find('.grid_col_settings_custom_field_'+$(this).val()+':last')
				.clone();

			// Enable inputs
			settings.find(':input').removeAttr('disabled');

			var customSettingsContainer = $(this)
				.parents('.grid-item')
				.find('.grid-col-settings-custom');

			var new_namespace = customSettingsContainer.parents('.grid-item').attr('data-field-name');
			var old_namespace = '(new_)?[0-9]{1,}';

			// Namespace fieldnames for the current column
			settings.html(
				settings.html().replace(
					RegExp('name="grid\\[cols\\]\\[' + old_namespace + '\\]', 'g'),
					'name="grid[cols][' + new_namespace + ']'
				)
			);

			// Find the container holding the settings form, replace its contents
			customSettingsContainer.html(settings);

			// Fire displaySettings event
			that._fireEvent('displaySettings', settings);
		});
	},

	/**
	 * Clones an element and copies over any form input values because
	 * normal cloning won't handle that
	 *
	 * @param	{jQuery Object}	el	Element to clone
	 * @return	{jQuery Object}	Cloned element with form fields populated
	 */
	_cloneWithFormValues: function(el) {
		var cloned = el.clone();

		el.find(":input:enabled").each(function() {
			// Find the new input in the cloned column for editing
			var new_input = cloned.find(":input[name='"+$(this).attr('name')+"']:enabled");

			if ($(this).is("select")) {
				new_input
					.find('option')
					.removeAttr('selected')
					.filter('[value="'+$(this).val()+'"]')
					.attr('selected', 'selected');
			}
			// Handle checkboxes
			else if ($(this).attr('type') == 'checkbox') {
				// .prop('checked', true) doesn't work, must set the attribute
				new_input.attr('checked', $(this).attr('checked'));
			}
			// Handle radio buttons
			else if ($(this).attr('type') == 'radio') {
				new_input
					.removeAttr('selected')
					.filter("[value='"+$(this).val()+"']")
					.attr('checked', $(this).attr('checked'));
			}
			// Handle textareas
			else if ($(this).is("textarea")) {
				new_input.html($(this).val());
			}
			// Everything else should handle the value attribute
			else {
				// .val('new val') doesn't work, must set the attribute
				new_input.attr('value', $(this).val());
			}
		});

		return cloned;
	},

	/**
	 * Called after main initialization to fire the 'display' event
	 * on pre-exising columns
	 */
	_settingsDisplay: function() {
		var that = this;
		this.root.find('.grid-item').each(function() {
			// Fire displaySettings event
			that._fireEvent('displaySettings', $('.grid-col-settings-custom > div', this));
		});
	},

	/**
	 * Fires event to fieldtype callbacks
	 *
	 * @param	{string}		action	Action name
	 * @param	{jQuery object}	el		jQuery object of affected element
	 */
	_fireEvent: function(action, el) {
		var fieldtype = el.data('fieldtype');

		// If no events regsitered, don't bother
		if (Grid._eventHandlers[action] === undefined ||
			Grid._eventHandlers[action][fieldtype] == undefined) {
			return;
		}

		Grid._eventHandlers[action][fieldtype]($(el));
	}
};

/**
 * Public method to instantiate Grid field
 */
EE.grid = function(field, settings) {
	return new Grid.Publish(field, settings);
};

/**
 * Public method to instantiate Grid settings
 */
EE.grid_settings = function(settings) {
	return new Grid.Settings(settings);
};

if (typeof _ !== 'undefined' && EE.grid_cache !== 'undefined') {
	_.each(EE.grid_cache, function(args) {
		Grid.bind.apply(Grid, args);
	});
}

})(jQuery);
