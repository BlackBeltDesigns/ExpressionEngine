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
 * ExpressionEngine CP Comment Settings Class
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Comments extends Settings {

	/**
	 * General Settings
	 */
	public function index()
	{
		$vars['sections'] = array(
			array(
				array(
					'title' => 'enable_comments',
					'desc' => 'enable_comments_desc',
					'fields' => array(
						'enable_comments' => array(
							'type' => 'inline_radio',
							'choices' => array(
								'y' => 'enable',
								'n' => 'disable'
							)
						)
					)
				)
			),
			'options' => array(
				array(
					'title' => 'comment_word_censoring',
					'desc' => sprintf(lang('comment_word_censoring_desc'), cp_url('settings/word-censor')),
					'fields' => array(
						'comment_word_censoring' => array(
							'type' => 'inline_radio',
							'choices' => array(
								'y' => 'enable',
								'n' => 'disable'
							)
						)
					)
				),
				array(
					'title' => 'comment_moderation_override',
					'desc' => 'comment_moderation_override_desc',
					'fields' => array(
						'comment_moderation_override' => array('type' => 'yes_no')
					),
				),
				array(
					'title' => 'comment_edit_time_limit',
					'desc' => 'comment_edit_time_limit_desc',
					'fields' => array(
						'comment_edit_time_limit' => array('type' => 'text')
					)
				)
			)
		);

		ee()->form_validation->set_rules(array(
			array(
				'field' => 'comment_edit_time_limit',
				'label' => 'lang:comment_edit_time_limit',
				'rules' => 'integer'
			)
		));

		$base_url = cp_url('settings/comments');

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

		ee()->view->base_url = $base_url;
		ee()->view->ajax_validate = TRUE;
		ee()->view->cp_page_title = lang('comment_settings');
		ee()->view->save_btn_text = 'btn_save_settings';
		ee()->view->save_btn_text_working = 'btn_save_settings_working';

		ee()->cp->set_breadcrumb(cp_url('publish/comments'), lang('all_comments'));

		ee()->cp->render('_shared/form', $vars);
	}
}
// END CLASS

/* End of file Comments.php */
/* Location: ./system/expressionengine/controllers/cp/Settings/Comments.php */