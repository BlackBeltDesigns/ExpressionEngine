<?php

namespace EllisLab\ExpressionEngine\Controllers\Settings;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use CP_Controller;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2014, EllisLab, Inc.
 * @license		http://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine CP Debugging & Output Settings Class
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class DebugOutput extends Settings {

	/**
	 * General Settings
	 */
	public function index()
	{
		$vars['sections'] = array(
			array(
				array(
					'title' => 'enable_debugging',
					'desc' => 'enable_debugging_desc',
					'fields' => array(
						'debug' => array(
							'type' => 'inline_radio',
							'choices' => array(
								'1' => 'enable',
								'0' => 'disable'
							)
						)
					)
				),
				array(
					'title' => 'show_profiler',
					'desc' => 'show_profiler_desc',
					'fields' => array(
						'show_profiler' => array('type' => 'yes_no')
					)
				),
				array(
					'title' => 'template_debugging',
					'desc' => 'template_debugging_desc',
					'fields' => array(
						'template_debugging' => array('type' => 'yes_no')
					)
				)
			),
			'output_options' => array(
				array(
					'title' => 'gzip_output',
					'desc' => 'gzip_output_desc',
					'fields' => array(
						'gzip_output' => array('type' => 'yes_no')
					)
				),
				array(
					'title' => 'force_query_string',
					'desc' => 'force_query_string_desc',
					'fields' => array(
						'force_query_string' => array('type' => 'yes_no')
					)
				),
				array(
					'title' => 'send_headers',
					'desc' => 'send_headers_desc',
					'fields' => array(
						'send_headers' => array('type' => 'yes_no')
					)
				),
				array(
					'title' => 'redirect_method',
					'desc' => 'redirect_method_desc',
					'fields' => array(
						'redirect_method' => array(
							'type' => 'dropdown',
							'choices' => array(
								'redirect' => lang('redirect_method_opt_location'),
								'refresh' => lang('redirect_method_opt_refresh')
							)
						)
					)
				),
				array(
					'title' => 'max_caches',
					'desc' => 'max_caches_desc',
					'fields' => array(
						'max_caches' => array('type' => 'text')
					)
				),
			)
		);

		ee()->form_validation->set_rules(array(
			array(
				'field' => 'max_caches',
				'label' => 'lang:max_caches',
				'rules' => 'integer'
			)
		));

		$this->validateNonTextInputs($vars['sections']);

		$base_url = cp_url('settings/debug-output');

		if (AJAX_REQUEST)
		{
			ee()->form_validation->run_ajax();
			exit;
		}
		elseif (ee()->form_validation->run() !== FALSE)
		{
			if ($this->saveSettings($vars['sections']))
			{
				ee()->view->set_message('success', lang('preferences_updated'), lang('preferences_updated_desc'), TRUE);
			}

			ee()->functions->redirect($base_url);
		}
		elseif (ee()->form_validation->errors_exist())
		{
			ee()->view->set_message('issue', lang('settings_save_error'), lang('settings_save_error_desc'));
		}

		ee()->view->base_url = $base_url;
		ee()->view->ajax_validate = TRUE;
		ee()->view->cp_page_title = lang('debugging_output');
		ee()->view->save_btn_text = 'btn_save_settings';
		ee()->view->save_btn_text_working = 'btn_save_settings_working';
		ee()->cp->render('settings/form', $vars);
	}
}
// END CLASS

/* End of file DebugOutput.php */
/* Location: ./system/EllisLab/ExpressionEngine/Controllers/Settings/DebugOutput.php */