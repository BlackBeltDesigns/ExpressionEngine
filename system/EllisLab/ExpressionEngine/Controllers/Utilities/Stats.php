<?php

namespace EllisLab\ExpressionEngine\Controllers\Utilities;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use EllisLab\ExpressionEngine\Library\CP\URL;

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
 * ExpressionEngine CP Statistics Class
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Stats extends Utilities {
	private $forums_exist	= FALSE;
	private $sources		= array('members', 'channel_titles', 'sites');

	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();

		if ( ! ee()->cp->allowed_group('can_access_tools', 'can_access_data'))
		{
			show_error(lang('unauthorized_access'));
		}

		// Do the forums exist?
		if (ee()->config->item('forum_is_installed') == "y")
		{
			$query = ee()->db->query("SELECT COUNT(*) AS count FROM exp_modules WHERE module_name = 'Forum'");

			if ($query->row('count')  > 0)
			{
				$this->forums_exist = TRUE;
				$this->sources = array_merge($this->sources, array('forums', 'forum_topics'));
			}
		}
	}

	/**
	 * Determine's the default language and lists those files.
	 */
	public function index()
	{
		$vars = array(
			'highlight'					=> 'source',
			'source_sort_url'			=> '',
			'record_count_sort_url'		=> '',
			'source_direction'			=> 'asc',
			'record_count_direction'	=> 'asc',
			'sources'					=> array()
		);

		// Determine and set the source sort
		$base_url = new URL('utilities/stats', ee()->session->session_id());
		if (ee()->input->get('source_direction') == 'desc')
		{
			$base_url->setQueryStringVariable('source_direction', 'asc');
			$vars['source_direction'] = 'desc';
			rsort($this->sources);
		}
		else
		{
			$base_url->setQueryStringVariable('source_direction', 'desc');
			sort($this->sources);
		}
		$vars['source_sort_url'] = $base_url->compile();

		foreach ($this->sources as $source)
		{
			$vars['sources'][$source] = ee()->db->count_all($source);
		}

		// Determine and set the record count sort
		$base_url = new URL('utilities/stats', ee()->session->session_id());
		$base_url->setQueryStringVariable('record_count_direction', 'desc');

		if (ee()->input->get('record_count_direction'))
		{
			$vars['highlight'] = 'record_count';
		}

		if (ee()->input->get('record_count_direction') == 'desc')
		{
			$base_url->setQueryStringVariable('record_count_direction', 'asc');
			$vars['record_count_direction'] = 'desc';
			arsort($vars['sources']);
		}
		elseif (ee()->input->get('record_count_direction') == 'asc')
		{
			asort($vars['sources']);
		}
		$vars['record_count_sort_url'] = $base_url->compile();

		ee()->view->cp_page_title = lang('manage_stats');

		ee()->cp->render('utilities/stats', $vars);
	}

	// @TODO This begs to be done via new Models (or a Service?)
	public function sync($source = NULL)
	{
		$sources = ee()->input->post('selection') ?: array($source);
		$sources = array_intersect($sources, $this->sources);

		if (empty($sources))
		{
			show_404();
		}

		if (in_array('members', $this->sources))
		{
			$member_entries = array(); // arrays of statements to update

			$member_entries_count = $this->db->query('SELECT COUNT(*) AS count, author_id FROM exp_channel_titles GROUP BY author_id ORDER BY count DESC');

			if (isset($this->cp->installed_modules['comment']))
			{
				$member_comments_count = $this->db->query('SELECT COUNT(*) AS count, author_id FROM exp_comments GROUP BY author_id ORDER BY count DESC');
			}

			$member_message_count = $this->db->query('SELECT COUNT(*) AS count, recipient_id FROM exp_message_copies WHERE message_read = "n" GROUP BY recipient_id ORDER BY count DESC');

			$member_data = array();

			if ($member_entries_count->num_rows() > 0)
			{

				foreach ($member_entries_count->result() as $row)
				{
					$member_entries[$row->author_id]['member_id'] = $row->author_id;
					$member_entries[$row->author_id]['total_entries'] = $row->count;
					$member_entries[$row->author_id]['total_comments'] = 0;
					$member_entries[$row->author_id]['private_messages'] = 0;
					$member_entries[$row->author_id]['total_forum_posts'] = 0;
					$member_entries[$row->author_id]['total_forum_topics'] = 0;
				}
			}

			if ($this->cp->installed_modules['comment'])
			{
				if ($member_comments_count->num_rows() > 0)
				{
					foreach ($member_comments_count->result() as $row)
					{
						if (isset($member_entries[$row->author_id]['member_id']))
						{
							$member_entries[$row->author_id]['total_comments'] = $row->count;
						}
						else
						{
							$member_entries[$row->author_id]['member_id'] = $row->author_id;
							$member_entries[$row->author_id]['total_entries'] = 0;
							$member_entries[$row->author_id]['total_comments'] = $row->count;
							$member_entries[$row->author_id]['private_messages'] = 0;
							$member_entries[$row->author_id]['total_forum_posts'] = 0;
							$member_entries[$row->author_id]['total_forum_topics'] = 0;
						}
					}
				}
			}

			if ($member_message_count->num_rows() > 0)
			{
				foreach ($member_message_count->result() as $row)
				{
					if (isset($member_entries[$row->recipient_id]['member_id']))
					{
						$member_entries[$row->recipient_id]['private_messages'] = $row->count;
					}
					else
					{
						$member_entries[$row->recipient_id]['member_id'] = $row->recipient_id;
						$member_entries[$row->recipient_id]['total_entries'] = 0;
						$member_entries[$row->recipient_id]['total_comments'] = 0;
						$member_entries[$row->recipient_id]['private_messages'] = $row->count;

						$member_entries[$row->recipient_id]['total_forum_posts'] = 0;
						$member_entries[$row->recipient_id]['total_forum_topics'] = 0;
					}
				}
			}

			if ($this->forums_exist === TRUE)
			{
				$forum_topics_count = $this->db->query('SELECT COUNT(*) AS count, author_id FROM exp_forum_topics GROUP BY author_id ORDER BY count DESC');
				$forum_posts_count = $this->db->query('SELECT COUNT(*) AS count, author_id FROM exp_forum_posts GROUP BY author_id ORDER BY count DESC');

				if ($forum_topics_count->num_rows() > 0)
				{
					foreach($forum_topics_count->result() as $row)
					{
						if (isset($member_entries[$row->author_id]['member_id']))
						{
							$member_entries[$row->author_id]['total_forum_topics'] = $row->count;
						}
						else
						{
							$member_entries[$row->author_id]['member_id'] = $row->author_id;
							$member_entries[$row->author_id]['total_entries'] = 0;
							$member_entries[$row->author_id]['total_comments'] = 0;
							$member_entries[$row->author_id]['private_messages'] = 0;
							$member_entries[$row->author_id]['total_forum_posts'] = 0;
							$member_entries[$row->author_id]['total_forum_topics'] = $row->count;
						}
					}
				}

				if ($forum_posts_count->num_rows() > 0)
				{
					foreach($forum_posts_count->result() as $row)
					{
						if (isset($member_entries[$row->author_id]['member_id']))
						{
							$member_entries[$row->author_id]['total_forum_posts'] = $row->count;
						}
						else
						{
							$member_entries[$row->author_id]['member_id'] = $row->author_id;
							$member_entries[$row->author_id]['total_entries'] = 0;
							$member_entries[$row->author_id]['total_comments'] = 0;
							$member_entries[$row->author_id]['private_messages'] = 0;
							$member_entries[$row->author_id]['total_forum_posts'] = $row->count;
							$member_entries[$row->author_id]['total_forum_topics'] = 0;
						}
					}
				}
			}

			if ( ! empty($member_entries))
			{
				$this->db->update_batch('exp_members', $member_entries, 'member_id');

				// Set the rest to 0 for all of the above

				$data = array(
					'total_entries'			=> 0,
					'total_comments'		=> 0,
					'private_messages'		=> 0,
					'total_forum_posts'		=> 0,
					'total_forum_topics'	=> 0
				);

				$this->db->where_not_in('member_id', array_keys($member_entries));
				$this->db->update('members', $data);
			}
		}

		if (in_array('channel_titles', $this->sources))
		{
			$channel_titles = array(); // arrays of statements to update

			if (isset($this->cp->installed_modules['comment']))
			{
				$channel_comments_count = $this->db->query('SELECT COUNT(comment_id) AS count, entry_id FROM exp_comments WHERE status = "o" GROUP BY entry_id ORDER BY count DESC');
				$channel_comments_recent = $this->db->query('SELECT MAX(comment_date) AS recent, entry_id FROM exp_comments WHERE status = "o" GROUP BY entry_id ORDER BY recent DESC');

				if ($channel_comments_count->num_rows() > 0)
				{
					foreach ($channel_comments_count->result() as $row)
					{
						$channel_titles[$row->entry_id]['entry_id'] = $row->entry_id;
						$channel_titles[$row->entry_id]['comment_total'] = $row->count;
						$channel_titles[$row->entry_id]['recent_comment_date'] = 0;
					}

					// Now for the most recent date
					foreach ($channel_comments_recent->result() as $row)
					{
						$channel_titles[$row->entry_id]['recent_comment_date'] = $row->recent;
					}
				}
			}

			// Set the rest to 0 for all of the above
			$data = array(
           		'comment_total'			=> 0,
           		'recent_comment_date'	=> 0
         	);

			if (count($channel_titles) > 0)
			{
				$this->db->update_batch('exp_channel_titles', $channel_titles, 'entry_id');

				$this->db->where_not_in('entry_id', array_keys($channel_titles));
				$this->db->update('channel_titles', $data);
			}
			else
			{
				$this->db->update('channel_titles', $data);
			}
		}

		if (in_array('forums', $this->sources))
		{
			$query = $this->db->query("SELECT forum_id FROM exp_forums WHERE forum_is_cat = 'n'");

			if ($query->num_rows() > 0)
			{
				foreach ($query->result_array() as $row)
				{
					$forum_id = $row['forum_id'];

					$res1 = $this->db->query("SELECT COUNT(*) AS count FROM exp_forum_topics WHERE forum_id = '{$forum_id}'");
					$total1 = $res1->row('count');

					$res2 = $this->db->query("SELECT COUNT(*) AS count FROM exp_forum_posts WHERE forum_id = '{$forum_id}'");
					$total2 = $res2->row('count');

					$this->db->query("UPDATE exp_forums SET forum_total_topics = '{$total1}', forum_total_posts = '{$total2}' WHERE forum_id = '{$forum_id}'");
				}
			}
		}

		if (in_array('forum_topics', $this->sources))
		{
			$total_rows = $this->db->count_all('forum_topics');

			$query = $this->db->query("SELECT forum_id FROM exp_forums WHERE forum_is_cat = 'n' ORDER BY forum_id");

			foreach ($query->result_array() as $row)
			{
				$forum_id = $row['forum_id'];

				$query = $this->db->query("SELECT COUNT(*) AS count FROM exp_forum_topics WHERE forum_id = '{$forum_id}'");
				$data['forum_total_topics'] = $query->row('count');

				$query = $this->db->query("SELECT COUNT(*) AS count FROM exp_forum_posts WHERE forum_id = '{$forum_id}'");
				$data['forum_total_posts'] = $query->row('count');

				$query = $this->db->query("SELECT topic_id, title, topic_date, last_post_date, last_post_author_id, screen_name
									FROM exp_forum_topics, exp_members
									WHERE member_id = last_post_author_id
									AND forum_id = '{$forum_id}'
									ORDER BY last_post_date DESC LIMIT 1");

				$data['forum_last_post_id'] 		= ($query->num_rows() == 0) ? 0 : $query->row('topic_id') ;
				$data['forum_last_post_title'] 		= ($query->num_rows() == 0) ? '' : $query->row('title') ;
				$data['forum_last_post_date'] 		= ($query->num_rows() == 0) ? 0 : $query->row('topic_date') ;
				$data['forum_last_post_author_id']	= ($query->num_rows() == 0) ? 0 : $query->row('last_post_author_id') ;
				$data['forum_last_post_author']		= ($query->num_rows() == 0) ? '' : $query->row('screen_name') ;

				$query = $this->db->query("SELECT post_date, author_id, screen_name
									FROM exp_forum_posts, exp_members
									WHERE  member_id = author_id
									AND forum_id = '{$forum_id}'
									ORDER BY post_date DESC LIMIT 1");

				if ($query->num_rows() > 0)
				{
					if ($query->row('post_date')  > $data['forum_last_post_date'])
					{
						$data['forum_last_post_date'] 		= $query->row('post_date');
						$data['forum_last_post_author_id']	= $query->row('author_id');
						$data['forum_last_post_author']		= $query->row('screen_name');
					}
				}

				$this->db->query($this->db->update_string('exp_forums', $data, "forum_id='{$forum_id}'"));
				unset($data);

				/** -------------------------------------
				/**  Update
				/** -------------------------------------*/

				$query = $this->db->query("SELECT forum_id FROM exp_forums");

				$total_topics = 0;
				$total_posts  = 0;

				foreach ($query->result_array() as $row)
				{
					$q = $this->db->query("SELECT COUNT(*) AS count FROM exp_forum_topics WHERE forum_id = '".$row['forum_id']."'");
					$total_topics = ($total_topics == 0) ? $q->row('count')  : $total_topics + $q->row('count') ;

					$q = $this->db->query("SELECT COUNT(*) AS count FROM exp_forum_posts WHERE forum_id = '".$row['forum_id']."'");
					$total_posts = ($total_posts == 0) ? $q->row('count')  : $total_posts + $q->row('count') ;
				}

				$this->db->query("UPDATE exp_stats SET total_forum_topics = '{$total_topics}', total_forum_posts = '{$total_posts}'");
			}

			$query = $this->db->query("SELECT topic_id FROM exp_forum_topics WHERE thread_total <= 1");

			if ($query->num_rows() > 0)
			{
				foreach ($query->result_array() as $row)
				{
					$res = $this->db->query("SELECT COUNT(*) AS count FROM exp_forum_posts WHERE topic_id = '".$row['topic_id']."'");
					$count = ($res->row('count') == 0) ? 1 : $res->row('count')  + 1;

					$this->db->query("UPDATE exp_forum_topics SET thread_total = '{$count}' WHERE topic_id = '".$row['topic_id']."'");
				}
			}
		}

		if (in_array('sites', $this->sources))
		{
			$original_site_id = $this->config->item('site_id');

			$query = $this->db->query("SELECT site_id FROM exp_sites");

			foreach($query->result_array() as $row)
			{
				$this->config->set_item('site_id', $row['site_id']);

				if (isset($this->cp->installed_modules['comment']))
				{
					$this->stats->update_comment_stats();
				}

				$this->stats->update_member_stats();
				$this->stats->update_channel_stats();
			}

			$this->config->set_item('site_id', $original_site_id);
		}

		ee()->view->set_message('success', lang('sync_completed'), '', TRUE);
		ee()->functions->redirect(cp_url('utilities/stats'));
	}
}
// END CLASS

/* End of file Stats.php */
/* Location: ./system/EllisLab/ExpressionEngine/Controllers/Utilities/Stats.php */