<?php

namespace EllisLab\ExpressionEngine\Controllers\Publish;

use EllisLab\ExpressionEngine\Controllers\Publish\AbstractPublish as AbstractPublishController;

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
 * ExpressionEngine CP Publish Class
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Publish extends AbstractPublishController {

	public function autosave($channel_id, $entry_id)
	{
		$site_id = ee()->config->item('site_id');

		$autosave = ee('Model')->get('ChannelEntryAutosave')
			->filter('original_entry_id', $entry_id)
			->filter('site_id', $site_id)
			->filter('channel_id', $channel_id)
			->first();

		if ( ! $autosave)
		{
			$autosave = ee('Model')->make('ChannelEntryAutosave');
			$autosave->original_entry_id = $entry_id;
			$autosave->site_id = $site_id;
			$autosave->channel_id = $channel_id;
		}

		$autosave->author_id = ee()->input->post('author_id');
		$autosave->title = (ee()->input->post('title')) ?: 'autosave_' . ee()->localize->now;
		$autosave->url_title = (ee()->input->post('url_title')) ?: 'autosave_' . ee()->localize->now;
		$autosave->status = ee()->input->post('status');
		$autosave->entry_date = ee()->input->post('entry_date');
		$autosave->entry_data = $_POST;

		$autosave->save();

		$time = ee()->localize->human_time(ee()->localize->now);
		$time = trim(strstr($time, ' '));

		$alert = ee('Alert')->makeInline()
			->asWarning()
			->cannotClose()
			->addToBody(lang('autosave_success') . $time);

		ee()->output->send_ajax_response(array(
			'success' => $alert->render(),
			'autosave_entry_id' => $autosave->entry_id,
			'original_entry_id'	=> $entry_id
		));
	}

}
// EOF