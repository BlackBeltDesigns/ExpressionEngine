<?php

namespace EllisLab\ExpressionEngine\Controllers\Publish;

use EllisLab\ExpressionEngine\Library\CP\Pagination;
use EllisLab\ExpressionEngine\Library\CP\Table;
use EllisLab\ExpressionEngine\Library\CP\URL;
use EllisLab\ExpressionEngine\Controllers\Publish\Publish;
use EllisLab\ExpressionEngine\Service\Model\Query\Builder;

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
 * ExpressionEngine CP Publish/Comments Class
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Comments extends Publish {

	public function __construct()
	{
		parent::__construct();

		if ( ! ee()->cp->allowed_group('can_moderate_comments')
		  && ! ee()->cp->allowed_group('can_edit_all_comments')
		  && ! ee()->cp->allowed_group('can_edit_own_comments'))
		{
			show_error(lang('unauthorized_access'));
		}
	}

	/**
	 * Displays all available comments
	 *
	 * @return void
	 */
	public function index()
	{
		if (ee()->input->post('bulk_action'))
		{
			$this->performBulkActions();
			ee()->functions->redirect(cp_url('publish/comments', ee()->cp->get_url_state()));
		}

		$vars = array();
		$channel = NULL;
		$base_url = new URL('publish/comments', ee()->session->session_id());

		$comments = ee('Model')->get('Comment')
			->filter('site_id', ee()->config->item('site_id'));

		$channel_filter = $this->createChannelFilter();
		if ($channel_filter->value())
		{
			$comments->filter('channel_id', $channel_filter->value());
			$channel = ee('Model')->get('Channel', $channel_filter->value())->first();
		}

		$status_filter = $this->createStatusFilter();
		if ($status_filter->value())
		{
			$comments->filter('status', $status_filter->value());
			$comments->filter('comment', 'LIKE', '%' . ee()->view->search_value . '%');
		}

		ee()->view->search_value = ee()->input->get_post('search');
		if ( ! empty(ee()->view->search_value))
		{
			$base_url->setQueryStringVariable('search', ee()->view->search_value);
		}

		$filters = ee('Filter')
			->add($channel_filter)
			->add($status_filter)
			->add('Date');

		$filter_values = $filters->values();

		if ( ! empty($filter_values['filter_by_date']))
		{
			if (is_array($filter_values['filter_by_date']))
			{
				$comments->filter('comment_date', '>=', $filter_values['filter_by_date'][0]);
				$comments->filter('comment_date', '<', $filter_values['filter_by_date'][1]);
			}
			else
			{
				$comments->filter('comment_date', '>=', ee()->localize->now - $filter_values['filter_by_date']);
			}
		}

		$count = $comments->count();

		// Add this last to get the right $count
		$filters->add('Perpage', $count, 'all_entries');

		ee()->view->filters = $filters->render($base_url);

		$filter_values = $filters->values();
		$base_url->addQueryStringVariables($filter_values);

		$page = ((int) ee()->input->get('page')) ?: 1;
		$offset = ($page - 1) * $filter_values['perpage']; // Offset is 0 indexed

		$comments->limit($filter_values['perpage'])
			->offset($offset);

		$table = $this->buildTableFromCommentQuery($comments);

		$vars['table'] = $table->viewData($base_url);
		$vars['form_url'] = $vars['table']['base_url'];

		$pagination = new Pagination($filter_values['perpage'], $count, $page);
		$vars['pagination'] = $pagination->cp_links($base_url);

		ee()->javascript->set_global('lang.remove_confirm', lang('comment') . ': <b>### ' . lang('comments') . '</b>');
		ee()->cp->add_js_script(array(
			'file' => array(
				'cp/v3/confirm_remove',
			),
		));

		if ($channel)
		{
			ee()->view->cp_breadcrumbs = array(
				cp_url('publish/edit', array('filter_by_channel' => $channel->channel_id)) => sprintf(lang('all_channel_entries'), $channel->channel_title),
			);
		}
		else
		{
			ee()->view->cp_breadcrumbs = array(
				cp_url('publish/edit') => sprintf(lang('all_channel_entries'), $channel),
			);
		}

		ee()->view->cp_page_title = lang('all_comments');

		// Set the page heading
		if ( ! empty(ee()->view->search_value))
		{
			ee()->view->cp_heading = sprintf(lang('search_results_heading'), $count, ee()->view->search_value);
		}
		else
		{
			ee()->view->cp_heading = lang('all_comments');
		}

		ee()->cp->render('publish/comments/index', $vars);
	}

	/**
	 * Dilsplays all comments for a given entry
	 *
	 * @param int $entry_id The ID# of the entry in question
	 * @return void
	 */
	public function entry($entry_id)
	{
		if (ee()->input->post('bulk_action'))
		{
			$this->performBulkActions();
			ee()->functions->redirect(cp_url('publish/comments/entry/' . $entry_id, ee()->cp->get_url_state()));
		}

		$vars = array();
		$base_url = new URL('publish/comments/entry/' . $entry_id, ee()->session->session_id());

		$entry = ee('Model')->get('ChannelEntry', $entry_id)->first();

		if ( ! $entry)
		{
			show_error(lang('no_entries_matching_that_criteria'));
		}

		$comments = ee('Model')->get('Comment')
			->filter('site_id', ee()->config->item('site_id'))
			->filter('entry_id', $entry_id);

		$status_filter = $this->createStatusFilter();
		if ($status_filter->value())
		{
			$comments->filter('status', $status_filter->value());
		}

		ee()->view->search_value = ee()->input->get_post('search');
		if ( ! empty(ee()->view->search_value))
		{
			$base_url->setQueryStringVariable('search', ee()->view->search_value);
			$comments->filter('comment', 'LIKE', '%' . ee()->view->search_value . '%');
		}

		$filters = ee('Filter')
			->add($status_filter)
			->add('Date');

		$filter_values = $filters->values();

		if ( ! empty($filter_values['filter_by_date']))
		{
			if (is_array($filter_values['filter_by_date']))
			{
				$comments->filter('comment_date', '>=', $filter_values['filter_by_date'][0]);
				$comments->filter('comment_date', '<', $filter_values['filter_by_date'][1]);
			}
			else
			{
				$comments->filter('comment_date', '>=', ee()->localize->now - $filter_values['filter_by_date']);
			}
		}

		$count = $comments->count();

		// Add this last to get the right $count
		$filters->add('Perpage', $count, 'all_entries');

		ee()->view->filters = $filters->render($base_url);

		$filter_values = $filters->values();
		$base_url->addQueryStringVariables($filter_values);

		$page = ((int) ee()->input->get('page')) ?: 1;
		$offset = ($page - 1) * $filter_values['perpage']; // Offset is 0 indexed

		$comments->limit($filter_values['perpage'])
			->offset($offset);

		$table = $this->buildTableFromCommentQuery($comments);

		$vars['table'] = $table->viewData($base_url);
		$vars['form_url'] = $vars['table']['base_url'];

		$pagination = new Pagination($filter_values['perpage'], $count, $page);
		$vars['pagination'] = $pagination->cp_links($base_url);

		ee()->javascript->set_global('lang.remove_confirm', lang('comment') . ': <b>### ' . lang('comments') . '</b>');
		ee()->cp->add_js_script(array(
			'file' => array(
				'cp/v3/confirm_remove',
			),
		));

		ee()->view->cp_breadcrumbs = array(
			cp_url('publish/edit', array('filter_by_channel' => $entry->channel_id)) => sprintf(lang('all_channel_entries'), $entry->getChannel()->channel_title),
		);

		ee()->view->cp_page_title = sprintf(lang('all_comments_for_entry'), $entry->title);

		// Set the page heading
		if ( ! empty(ee()->view->search_value))
		{
			ee()->view->cp_heading = sprintf(lang('search_results_heading'), $count, ee()->view->search_value);
		}
		else
		{
			ee()->view->cp_heading = sprintf(lang('all_comments_for_entry'), $entry->title);
		}

		ee()->cp->render('publish/comments/index', $vars);
	}

	/**
	 * Builds a Table object from a Query of Comment model entitites
	 *
	 * @param Builder $comments A Query\Builder object for Comment model entities
	 * @return Table A Table instance
	 */
	private function buildTableFromCommentQuery(Builder $comments)
	{
		ee()->load->helper('text');
		$table = Table::create();

		$table->setColumns(
			array(
				'column_comment',
				'column_comment_date',
				'column_ip_address',
				'column_status' => array(
					'type'	=> Table::COL_STATUS
				),
				'manage' => array(
					'type'	=> Table::COL_TOOLBAR
				),
				array(
					'type'	=> Table::COL_CHECKBOX
				)
			)
		);
		$table->setNoResultsText(lang('no_comments'));

		$comments->order(str_replace('column_', '', $table->sort_col), $table->sort_dir);

		$data = array();

		$comment_id = ee()->session->flashdata('comment_id');

		foreach ($comments->all() as $comment)
		{
			switch ($comment->status)
			{
				case 'o':
					$status = lang('open');
					break;
				case 'c':
					$status = lang('closed');
					break;
				default:
					$status = lang("pending");
			}

			$column = array(
				ee('View')->make('publish/comments/partials/title')->render(array('comment' => $comment)),
				ee()->localize->human_time($comment->comment_date),
				$comment->ip_address,
				$status,
				array('toolbar_items' => array(
					'edit' => array(
						'href' => cp_url('publish/comment/edit/' . $comment->comment_id),
						'title' => lang('edit')
					)
				)),
				array(
					'name' => 'selection[]',
					'value' => $comment->comment_id,
					'data' => array(
						'confirm' => lang('comment') . ': <b>' . htmlentities(ellipsize($comment->comment, 50), ENT_QUOTES) . '</b>'
					)
				)
			);

			$attrs = array();

			if ($comment_id && $comment->comment_id == $comment_id)
			{
				$attrs = array('class' => 'selected');
			}

			$data[] = array(
				'attrs'		=> $attrs,
				'columns'	=> $column
			);

		}
		$table->setData($data);
		return $table;
	}

	private function createStatusFilter()
	{
		$status = ee('Filter')->make('filter_by_status', 'filter_by_status', array(
			'o' => lang('open'),
			'c' => lang('closed'),
			'p' => lang('pending')
		));
		$status->disableCustomValue();
		return $status;
	}

	private function performBulkActions()
	{
		switch(ee()->input->post('bulk_action'))
		{
			case 'remove':
				$this->remove(ee()->input->post('selection'));
				break;

			case 'remove':
				$this->setStatus(ee()->input->post('selection'), 'o');
				break;

			case 'closed':
				$this->setStatus(ee()->input->post('selection'), 'c');
				break;

			case 'pending':
				$this->setStatus(ee()->input->post('selection'), 'p');
				break;
		}
	}

	private function remove($comment_ids)
	{
		if ( ! is_array($comment_ids))
		{
			$comment_ids = array($comment_ids);
		}

		$comments = ee('Model')->get('Comment', $comment_ids)
			->filter('site_id', ee()->config->item('site_id'))
			->all();

		$comment_names = array();

		ee()->load->helper('text');

		foreach ($comments as $comment)
		{
			$comment_names[] = ellipsize($comment->comment, 50);
		}

		$comments->delete();

		ee('Alert')->makeInline('comments-form')
			->asSuccess()
			->withTitle(lang('success'))
			->addToBody(lang('comments_removed_desc'))
			->addToBody($comment_names)
			->defer();
	}

	private function setStatus($comment_ids, $status)
	{
		if ( ! is_array($comment_ids))
		{
			$comment_ids = array($comment_ids);
		}

		$comments = ee('Model')->get('Comment', $comment_ids)
			->filter('site_id', ee()->config->item('site_id'))
			->set('status', $status)
			->update();

		$comments = ee('Model')->get('Comment', $comment_ids)
			->filter('site_id', ee()->config->item('site_id'))
			->all();

		$comment_names = array();

		ee()->load->helper('text');

		foreach ($comments as $comment)
		{
			$comment_names[] = ellipsize($comment->comment, 50);
		}

		switch ($status)
		{
			case 'o':
				$status = lang('open');
				break;
			case 'c':
				$status = lang('closed');
				break;
			default:
				$status = lang("pending");
		}

		ee('Alert')->makeInline('comments-form')
			->asSuccess()
			->withTitle(lang('success'))
			->addToBody(sprintf(lang('comments_status_updated_desc'), strtolower($status)))
			->addToBody($comment_names)
			->defer();
	}
}
// EOF