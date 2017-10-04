<?php
/**
 * ExpressionEngine (https://expressionengine.com)
 *
 * @link      https://expressionengine.com/
 * @copyright Copyright (c) 2003-2017, EllisLab, Inc. (https://ellislab.com)
 * @license   https://expressionengine.com/license
 */

/**
 * Fluid Block Fieldtype
 */
class Fluid_block_ft extends EE_Fieldtype {

	public $info = array();

	public $has_array_data = TRUE;

	private $errors;

	/**
	 * Fetch the fieldtype's name and version from its addon.setup.php file.
	 */
	public function __construct()
	{
		$addon = ee('Addon')->get('fluid_block');
		$this->info = array(
			'name'    => $addon->getName(),
			'version' => $addon->getVersion()
		);
	}

	public function validate($field_data)
	{
		$this->errors = new \EllisLab\ExpressionEngine\Service\Validation\Result;

		if (empty($field_data))
		{
			return TRUE;
		}

		$field_templates = ee('Model')->get('ChannelField', $this->settings['field_channel_fields'])
			->order('field_label')
			->all()
			->indexByIds();

		foreach ($field_data['fields'] as $key => $data)
		{
			$field_id = NULL;
			$block_data_id = NULL;

			foreach (array_keys($data) as $datum)
			{
				if (strpos($datum, 'field_id_') === 0)
				{
					$field_id = str_replace('field_id_', '', $datum);
					break;
				}
			}

			$field_name = $this->name() . '[fields][' . $key . '][field_id_' . $field_id . ']';

			// Is this AJAX validation? If so, just return the result for the field
			// we're validating by skipping the others
			if (ee()->input->is_ajax_request() && strpos(ee()->input->post('ee_fv_field'), $field_name) === FALSE)
			{
				continue;
			}

			if (strpos($key, 'field_') === 0)
			{
				$block_data_id = (int) str_replace('field_', '', $key);
			}

			$field = clone $field_templates[$field_id];

			$f = $field->getField();
			$ft_instance = $f->getNativeField();

			if (isset($ft_instance->has_array_data)
				&& $ft_instance->has_array_data
				&& ! is_array($data['field_id_' . $field_id]))
			{
				$data['field_id_' . $field_id] = array();
			}

			$f->setName($field_name);
			$f = $this->setupFieldInstance($f, $data, $block_data_id);

			$validator = ee('Validation')->make();
			$validator->defineRule('validateField', function($key, $value, $parameters, $rule) use ($f) {
				return $f->validate($value);
			});

			$validator->setRules(array(
				$f->getName() => 'validateField'
			));

			$result = $validator->validate(array($f->getName() => $f->getData()));

			if ($result->isNotValid())
			{
				foreach($result->getFailed() as $field_name => $rules)
				{
					foreach ($rules as $rule)
					{
						$this->errors->addFailed($field_name, $rule);
					}
				}
			}
		}

		if (ee()->input->is_ajax_request())
		{
			$errors = $this->errors->getErrors($field_name);
			return $errors['callback'];
		}

		return ($this->errors->isValid()) ? TRUE : 'form_validation_error';
	}

	// Actual saving takes place in post_save so we have an entry_id
	public function save($data)
	{
		if (is_null($data))
		{
			$data = array();
		}

		ee()->session->set_cache(__CLASS__, $this->name(), $data);

		$compiled_data_for_search = array();

		foreach ($data['fields'] as $field_data)
		{
			foreach ($field_data as $key => $value)
			{
				if (strpos($key, '_id_') and is_string($value))
				{
					$compiled_data_for_search[] = $value;
				}
			}
		}

		return implode(' ', $compiled_data_for_search);
	}

	public function post_save($data)
	{
		// Prevent saving if save() was never called, happens in Channel Form
		// if the field is missing from the form
		if (($data = ee()->session->cache(__CLASS__, $this->name(), FALSE)) === FALSE)
		{
			return;
		}

		$blockData = $this->getBlockData()->indexBy('id');

		$i = 1;
		foreach ($data['fields'] as $key => $value)
		{
			if ($key == 'new_field_0')
			{
				continue;
			}

			// Existing field
			if (strpos($key, 'field_') === 0)
			{
				$id = str_replace('field_', '', $key);
				$this->updateField($blockData[$id], $i, $value);
				unset($blockData[$id]);
			}
			// New field
			elseif (strpos($key, 'new_field_') === 0)
			{
				foreach (array_keys($value) as $k)
				{
					if (strpos($k, 'field_id_') === 0)
					{
						$field_id = str_replace('field_id_', '', $k);
						$this->addField($i, $field_id, $value);
						break;
					}
				}
			}

			$i++;
		}

		// Remove fields
		foreach ($blockData as $block)
		{
			$this->removeField($block);
		}
	}

	private function prepareData($block, array $values)
	{
		$field_data = $block->getFieldData();
		$field_data->set($values);
		$field = $block->getField($field_data);
		$field->setItem('block_data_id', $block->getId());
		$field->save();

		$values['field_id_' . $field->getId()] = $field->getData();

		$field->postSave();

		$format = $field->getFormat();

		if ( ! is_null($format))
		{
			$values['field_ft_' . $field->getId()] = $format;
		}

		$timezone = $field->getTimezone();

		if ( ! is_null($timezone))
		{
			$values['field_dt_' . $field->getId()] = $timezone;
		}

		return $values;
	}

	private function updateField($block, $order, array $values)
	{
		$values = $this->prepareData($block, $values);

		$block->order = $order;
		$block->save();

		$query = ee('Model/Datastore')->rawQuery();
		$query->set($values);
		$query->where('id', $block->field_data_id);
		$query->update($block->ChannelField->getTableName());
	}

	private function addField($order, $field_id, array $values)
	{
		$block = ee('Model')->make('fluid_block:FluidBlock');
		$block->block_id = $this->field_id;
		$block->entry_id = $this->content_id;
		$block->field_id = $field_id;
		$block->order = $order;
		$block->field_data_id = 0;
		$block->save();

		$values = $this->prepareData($block, $values);

		$values = array_merge($values, array(
			'entry_id' => 0,
		));

		$field = ee('Model')->get('ChannelField', $field_id)->first();

		$query = ee('Model/Datastore')->rawQuery();
		$query->set($values);
		$query->insert($field->getTableName());
		$id = $query->insert_id();

		$block->field_data_id = $id;
		$block->save();
	}

	private function removeField($block)
	{
		$query = ee('Model/Datastore')->rawQuery();
		$query->where('id', $block->field_data_id);
		$query->delete($block->ChannelField->getTableName());

		$block->delete();
	}

	/**
	 * Displays the field for the CP or Frontend, and accounts for grid
	 *
	 * @param string $data Stored data for the field
	 * @return string Field display
	 */
	public function display_field($field_data)
	{
		$fields = '';

		$field_templates = ee('Model')->get('ChannelField', $this->settings['field_channel_fields'])
			->order('field_label')
			->all()
			->indexByIds();

		$filters = ee('View')->make('fluid_block:filters')->render(array('fields' => $field_templates));

		if ( ! is_array($field_data))
		{
			if ($this->content_id)
			{
				$blockData = $this->getBlockData();

				foreach ($blockData as $data)
				{
					$field = $data->getField();

					$field->setName($this->name() . '[fields][field_' . $data->getId() . '][field_id_' . $field->getId() . ']');

					$fields .= ee('View')->make('fluid_block:field')->render(array('field' => $data->ChannelField, 'filters' => $filters, 'errors' => $this->errors));
				}
			}

		}
		else
		{
			foreach ($field_data['fields'] as $key => $data)
			{
				$field_id = NULL;

				foreach (array_keys($data) as $datum)
				{
					if (strpos($datum, 'field_id_') === 0)
					{
						$field_id = str_replace('field_id_', '', $datum);
						break;
					}
				}

				$field = clone $field_templates[$field_id];

				$f = $field->getField();

				$f->setName($this->name() . '[fields][' . $key . '][field_id_' . $field->getId() . ']');

				$f = $this->setupFieldInstance($f, $data);

				$fields .= ee('View')->make('fluid_block:field')->render(array('field' => $field, 'filters' => $filters, 'errors' => $this->errors));
			}
		}

		$templates = '';

		foreach ($field_templates as $field)
		{
			$f = $field->getField();
			$f->setName($this->name() . '[fields][new_field_0][field_id_' . $field->getId() . ']');

			$templates .= ee('View')->make('fluid_block:field')->render(array('field' => $field, 'filters' => $filters, 'errors' => $this->errors));
		}

		if (REQ == 'CP')
		{
			ee()->cp->add_js_script(array(
				'ui' => array(
					'sortable'
				),
				'file' => array(
					'fields/fluid_block/cp',
					'cp/sort_helper'
				),
			));

			return ee('View')->make('fluid_block:publish')->render(array(
				'fields'         => $fields,
				'fieldTemplates' => $templates,
				'filters'        => $filters,
			));
		}
	}

	public function display_settings($data)
	{
		$custom_field_options = ee('Model')->get('ChannelField')
			->filter('site_id', 'IN', array(0, ee()->config->item('site_id')))
			->filter('field_type', '!=', 'fluid_block')
			->order('field_label')
			->all()
			->filter(function($field) {
				return $field->getField()->acceptsContentType('fluid_block');
			})
			->getDictionary('field_id', 'field_label');

		$settings = array(
			array(
				'title'     => 'custom_fields',
				'fields'    => array(
					'field_channel_fields' => array(
						'type' => 'checkbox',
						'wrap' => TRUE,
						'choices' => $custom_field_options,
						'value' => isset($data['field_channel_fields']) ? $data['field_channel_fields'] : array()
					)
				)
			),
		);

		if ( ! $this->isNew())
		{
			ee()->javascript->set_global(array(
				'fields.fluid_block.fields' => $data['field_channel_fields']
			));

			ee()->cp->add_js_script(array(
				'file' => 'fields/fluid_block/settings',
			));

			$modal = ee('View')->make('fluid_block:modal')->render();
			ee('CP/Modal')->addModal('remove-field', $modal);
		}

		return array('field_options_fluid_block' => array(
			'label' => 'field_options',
			'group' => 'fluid_block',
			'settings' => $settings
		));
	}

	public function save_settings($data)
	{
		$defaults = array(
			'field_channel_fields' => array(),
		);

		$all = array_merge($defaults, $data);

		$fields = ee('Model')->get('ChannelField', $data['field_channel_fields'])
			->filter('legacy_field_data', 'y')
			->all();

		foreach ($fields as $field)
		{
			$field->createTable();
		}

		if (isset($this->settings['field_channel_fields']))
		{
			$removed_fields = (array_diff($this->settings['field_channel_fields'], $data['field_channel_fields']));

			if ( ! empty($removed_fields))
			{
				$blockData = ee('Model')->get('fluid_block:FluidBlock')
					->filter('block_id', $this->field_id)
					->filter('field_id', 'IN', $removed_fields)
					->all()
					->delete();

				$fields = ee('Model')->get('ChannelField', $removed_fields)
					->fields('field_label')
					->all()
					->pluck('field_label');

				if ( ! empty($fields))
				{
					ee()->logger->log_action(sprintf(lang('removed_fields_from_fluid_block'), $this->settings['field_label'], '<b>' . implode('</b>, <b>', $fields) . '</b>'));
				}
			}
		}

		return array_intersect_key($all, $defaults);
	}

	public function settings_modify_column($data)
	{
		if (isset($data['ee_action']) && $data['ee_action'] == 'delete')
		{
			$blockData = ee('Model')->get('fluid_block:FluidBlock')
				->filter('block_id', $data['field_id'])
				->all()
				->delete();
		}

		return array();
	}

	/**
	 * Called when entries are deleted
	 *
	 * @param	array	Entry IDs to delete data for
	 */
	public function delete($entry_ids)
	{
		$blockData = ee('Model')->get('fluid_block:FluidBlock')
			->filter('block_id', $this->field_id)
			->filter('entry_id', 'IN', $entry_ids)
			->all()
			->delete();
	}

	/**
	 * Accept all content types.
	 *
	 * @param string  The name of the content type
	 * @return bool   Accepts all content types
	 */
	public function accepts_content_type($name)
	{
		$incompatible = array('grid', 'fluid_block');
		return ( ! in_array($name, $incompatible));
	}

	/**
	 * Update the fieldtype
	 *
	 * @param string $version The version being updated to
	 * @return boolean TRUE if successful, FALSE otherwise
	 */
	public function update($version)
	{
		return TRUE;
	}

	/**
	 * Gets the fluid block's data for a given block and entry
	 *
	 * @param int $block_id The id for the block
	 * @param int $entry_id The id for the entry
	 * @return obj A Collection of FluidBlock objects
	 */
	private function getBlockData($block_id = '', $entry_id = '')
	{
		$block_id = ($block_id) ?: $this->field_id;
		$entry_id = ($entry_id) ?: $this->content_id;

		$cache_key = "FluidBlock/{$block_id}/{$entry_id}";

		if (($blockData = ee()->session->cache("FluidBlock", $cache_key, FALSE)) === FALSE)
		{
			$blockData = ee('Model')->get('fluid_block:FluidBlock')
				->with('ChannelField')
				->filter('block_id', $block_id)
				->filter('entry_id', $entry_id)
				->order('order')
				->all();

			ee()->session->set_cache("FluidBlock", $cache_key, $blockData);
		}

		return $blockData;
	}

	/**
	 * Sets the data, format, and timzeone for a field
	 *
	 * @param FieldFacade $field The field
	 * @param array $data An associative array containing the data to set
	 * @return FieldFacade The field.
	 */
	private function setupFieldInstance($field, array $data, $block_data_id = NULL)
	{
		$field_id = $field->getId();

		$field->setContentId($this->content_id);

		$field->setData($data['field_id_' . $field_id]);

		if (isset($data['field_ft_' . $field_id]))
		{
			$field->setFormat($data['field_ft_' . $field_id]);
		}

		if (isset($data['field_dt_' . $field_id]))
		{
			$field->setTimezone($data['field_dt_' . $field_id]);
		}

		if ( ! is_null($block_data_id))
		{
			$field->setItem('block_data_id', $block_data_id);
		}

		return $field;
	}

	/**
	 * Replace Fluid Block template tags
	 */
	public function replace_tag($data, $params = '', $tagdata = '')
	{
		ee()->load->library('fluid_block_parser');

		// not in a channel scope? pre-process may not have been run.
		if ($this->content_type() != 'channel')
		{
			ee()->load->library('api');
			ee()->legacy_api->instantiate('channel_fields');
			ee()->grid_parser->fluid_block_field_names[$this->id()] = $this->name();
		}

		return ee()->fluid_block_parser->parse($this->row, $this->id(), $params, $tagdata, $this->content_type());
	}

}

// EOF
