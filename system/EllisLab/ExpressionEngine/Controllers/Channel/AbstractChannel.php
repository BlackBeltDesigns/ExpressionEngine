<?php

namespace EllisLab\ExpressionEngine\Controllers\Channel;

use CP_Controller;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2015, EllisLab, Inc.
 * @license		http://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine CP Abstract Channel Class
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
abstract class AbstractChannel extends CP_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		if ( ! ee()->cp->allowed_group(
			'can_access_admin',
			'can_admin_channels',
			'can_access_content_prefs'
		))
		{
			show_error(lang('unauthorized_access'));
		}

		ee()->lang->loadfile('channel');
		ee()->load->library('form_validation');

		// Register our menu
		ee()->menu->register_left_nav(array(
			'channels' => array(
				'href' => cp_url('channel'),
				'button' => array(
					'href' => cp_url('channel/create'),
					'text' => 'new'
				)
			),
			'custom_fields' => array(
				'href' => cp_url('channel/field'),
				'button' => array(
					'href' => cp_url('channel/field/create'),
					'text' => 'new'
				)
			),
			array(
				'field_groups' => cp_url('channel/field-group')
			),
			'category_groups' => array(
				'href' => cp_url('channel/cat'),
				'button' => array(
					'href' => cp_url('channel/cat/create'),
					'text' => 'new'
				)
			),
			'status_groups' => array(
				'href' => cp_url('channel/status'),
				'button' => array(
					'href' => cp_url('channel/status/create'),
					'text' => 'new'
				)
			)
		));

		// This header is section-wide
		ee()->view->header = array(
			'title' => lang('channel_manager'),
			'form_url' => cp_url('channel/search'),
			'toolbar_items' => array(
				'settings' => array(
					'href' => cp_url('settings/content-design'),
					'title' => lang('settings')
				)
			)
		);
	}

}
// EOF