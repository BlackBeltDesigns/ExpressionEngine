<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2013, EllisLab, Inc.
 * @license		http://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 2.6
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Channel Parser Component (Custom Field Pairs)
 *
 * @package		ExpressionEngine
 * @subpackage	Core
 * @category	Core
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class EE_Channel_custom_field_pair_parser implements EE_Channel_parser_component {

	/**
	 * Check if custom fields are enabled.
	 *
	 * @param array		A list of "disabled" features
	 * @return Boolean	Is disabled?
	 */
	public function disabled(array $disabled, EE_Channel_preparser $pre)
	{
		return in_array('custom_fields', $disabled) OR empty($pre->channel()->pfields);
	}

	// --------------------------------------------------------------------

	/**
	 * Find any {field} {/field} tag pair chunks in the template and
	 * extract them for easier parsing in the main loop.
	 *
	 * The returned chunks will be passed to replace() as a third parameter.
	 *
	 * @param String	The tagdata to be parsed
	 * @param Object	The preparser object.
	 * @return Array	The found custom field pair chunks
	 */
	public function pre_process($tagdata, EE_Channel_preparser $pre)
	{
		$pfield_chunk = array();

		$prefix = $pre->prefix();
		$parser = $pre->parser();
		$channel = $pre->channel();

		foreach ($channel->pfields as $site_id => $pfields)
		{
			$tagdata_replaced = FALSE;
			$pfield_names = array_intersect(
				$channel->cfields[$site_id],
				array_keys($pfields)
			);
			$pfield_chunk[$site_id] = array();

			foreach($pfield_names as $field_name => $field_id)
			{
				if ( ! $pre->has_tag_pair($field_name))
				{
					continue;
				}

				$pfield_chunk[$site_id][$field_name] =
					ee()->api_channel_fields->get_pair_field(
						$tagdata,
						$field_name,
						$prefix
					);

				// Update the tagdata with hashes
				if ( ! $tagdata_replaced)
				{
					foreach ($pfield_chunk[$site_id][$field_name] as $chunk_data)
					{
						$parser->set_tagdata(str_replace(
							$chunk_data[3], // the original tag
							$chunk_data[4], // the hash
							$tagdata
						));
					}

					$tagdata_replaced = TRUE;
				}
			}
		}

		return $pfield_chunk;
	}

	// ------------------------------------------------------------------------

	/**
	 * Replace all of the custom channel pair fields.
	 *
	 * @param String	The tagdata to be parsed
	 * @param Object	The channel parser object
	 * @param Mixed		The results from the preparse method
	 *
	 * @return String	The processed tagdata
	 */
	public function replace($tagdata, EE_Channel_data_parser $obj, $pfield_chunks)
	{
		$data = $obj->row();
		$prefix = $obj->prefix();

		$site_id = $data['site_id'];

		$cfields = $obj->channel()->cfields;
		$cfields = isset($cfields[$site_id]) ? $cfields[$site_id] : array();

		if (empty($cfields) OR ! isset($pfield_chunks[$site_id]))
		{
			return $tagdata;
		}

		$pfield_chunk = $pfield_chunks[$site_id];
		$ft_api = ee()->api_channel_fields;

		foreach ($pfield_chunk as $tag_name => $chunks)
		{
			$field_name = preg_replace('/^'.$prefix.'/', '', $tag_name);
			$field_name = substr($field_name, strpos($field_name, ' '));
			$field_id = $cfields[$field_name];

			$ft = $ft_api->setup_handler($field_id, TRUE);
			$ft_name = $ft_api->field_type;

			if ($ft)
			{
				$_ft_path = $ft_api->ft_paths[$ft_api->field_type];
				ee()->load->add_package_path($_ft_path, FALSE);

				$ft->_init(array(
					'row'			=> $data,
					'content_id'	=> $data['entry_id'],
					'content_type'	=> 'channel'
				));

				$pre_processed = $ft_api->apply('pre_process', array(
					$data['field_id_'.$field_id]
				));

				foreach($chunks as $chk_data)
				{
					// If some how the fieldtype that the channel fields
					// API is referencing changed to another fieldtype
					// (Grid may cause this), get it back on track to
					// parse the next chunk
					if ($ft_name != $ft_api->field_type)
					{
						$ft_api->setup_handler($field_id);
					}

					list($modifier, $content, $params, $chunk, $hash) = $chk_data;

					$tpl_chunk = '';
					// Set up parse function name based on whether or not
					// we have a modifier
					$parse_fnc = ($modifier) ? 'replace_'.$modifier : 'replace_tag';


					// -------------------------------------------
					// 'custom_field_modify_parameter' hook.
					// - Allow developers to modify the parameters array
					//
					// 	There are 3 ways to use this hook:
					// 	 	1) Add to the existing Active Record call, e.g. ee()->db->where('foo', 'bar');
					// 	 	2) Call ee()->db->_reset_select(); to terminate this AR call and start a new one
					// 	 	3) Call ee()->db->_reset_select(); and modify the currently compiled SQL string
					//
					//   All 3 require a returned query result array.
					//
					if (ee()->extensions->active_hook('custom_field_modify_parameter') === TRUE)
					{
						$related = ee()->extensions->call(
							'custom_field_modify_parameter',
							$entry_id,
							$this->field_id,
							ee()->db->_compile_select(FALSE, FALSE)
						);
					}

					if (method_exists($ft, $parse_fnc))
					{
						$tpl_chunk = $ft_api->apply($parse_fnc, array(
							$pre_processed,
							$params,
							$content
						));
					}
					// Go to catchall and include modifier
					elseif (method_exists($ft, 'replace_tag_catchall') AND $modifier !== '')
					{
						$tpl_chunk = $ft_api->apply('replace_tag_catchall', array(
							$pre_processed,
							$params,
							$content,
							$modifier
						));
					}

					$tagdata = str_replace($hash, $tpl_chunk, $tagdata);
				}

				ee()->load->remove_package_path($_ft_path);
			}
		}

		return $tagdata;
	}

	// -------------------------------------------------------------------------

	/**
	 * Modify the tagdata for a compnonent, used in conjunction with the
	 * channel_entries_tagdata hook
	 *
	 * @param  Array $callback       Callback data in an array to pass to
	 *                               call_user_func
	 * @param  Mixed $data           Data pulled out by preprocess
	 * @param  Array $row            Row of data from the Parser::parse() method
	 * @return String                Modified $data (mostly modified $tagdata)
	 */
	public function modify_tagdata($callback, $data, $row)
	{
		$site_id = $row['entry_site_id'];

		foreach ($data[$site_id] as $field_type => $instances)
		{
			foreach ($instances as $index => $instance)
			{
				$data[$site_id][$field_type][$index][3] = call_user_func($callback, $instance[3], $row);
			}
		}

		return $data;
	}
}
