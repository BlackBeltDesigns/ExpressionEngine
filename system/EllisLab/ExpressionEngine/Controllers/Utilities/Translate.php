<?php

namespace EllisLab\ExpressionEngine\Controllers\Utilities;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use ZipArchive;
use EllisLab\ExpressionEngine\Library\CP\Pagination;
use EllisLab\ExpressionEngine\Library\CP\Table;
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
 * ExpressionEngine CP Translate Manager Class
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Translate extends Utilities {

	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();

		if ( ! ee()->cp->allowed_group('can_access_tools', 'can_access_utilities'))
		{
			show_error(lang('unauthorized_access'));
		}

		if ( ! is_really_writable(APPPATH.'translations/'))
		{
			$not_writeable = lang('translation_dir_unwritable');
		}
	}

	/**
	 * Magic method that sets the language and routes the action
	 */
	public function __call($name, $arguments)
	{
		$name = strtolower($name);

		ee()->load->model('language_model');
		$languages =ee()->language_model->language_pack_names();
		if ( ! array_key_exists($name, $languages))
		{
			show_404();
		}

		if (empty($arguments))
		{
			$this->listFiles($name);
		}
		elseif (strtolower($arguments[0]) == 'edit' && isset($arguments[1]))
		{
			$this->edit($name, $arguments[1]);
		}
		elseif (strtolower($arguments[0]) == 'save' && isset($arguments[1]))
		{
			$this->save($name, $arguments[1]);
		}
		else
		{
			show_404();
		}
	}

	/**
	 * Determine's the default language and lists those files.
	 */
	public function index()
	{
		$language = ee()->config->item('deft_lang') ?: 'english';
		$this->listFiles($language);
	}

	/**
	 * List the "*_lang.php" files in a $language directory
	 *
	 * @param string $language	The language directory (i.e. 'english')
	 */
	private function listFiles($language)
	{
		if (ee()->input->get_post('bulk_action') == 'export')
		{
			$this->export($language, ee()->input->get_post('selection'));
		}

		ee()->view->cp_page_title = ucfirst($language) . ' ' . lang('language_files');
		$vars['language'] = $language;

		$base_url = new URL('utilities/translate/' . $language, ee()->session->session_id());

		$data = array();

		$this->load->helper('file');

		$path = APPPATH.'language/'.$language;
		$ext_len = strlen('.php');

		$filename_end = '_lang.php';
		$filename_end_len = strlen($filename_end);

		$languages = array();

		$language_files = get_filenames($path);

		foreach ($language_files as $file)
		{
			if ($file == 'email_data.php')
			{
				continue;
			}

			if (substr($file, -$filename_end_len) && substr($file, -$ext_len) == '.php')
			{
				$name = str_replace('_lang.php', '', $file);
				$data[] = array(
					'filename'	=> $file,
					array('toolbar_items' => array(
						'edit' => array(
							'href' => cp_url('utilities/translate/' . $language . '/edit/' . $name),
							'title' => strtolower(lang('edit'))
						)
					)),
					array(
						'name' => 'selection[]',
						'value' => $name
					)
				);
			}
		}

		$table = Table::create(array('autosort' => TRUE, 'autosearch' => TRUE));
		$table->setColumns(
			array(
				'file_name',
				'manage' => array(
					'type'	=> Table::COL_TOOLBAR
				),
				array(
					'type'	=> Table::COL_CHECKBOX
				)
			)
		);
		$table->setNoResultsText('no_search_results');
		$table->setData($data);
		$vars['table'] = $table->viewData($base_url);

		$base_url = $vars['table']['base_url'];

		if ( ! empty($vars['table']['data']))
		{
			// Paginate!
			$pagination = new Pagination(
				$vars['table']['limit'],
				$vars['table']['total_rows'],
				$vars['table']['page']
			);
			$vars['pagination'] = $pagination->cp_links($base_url);
		}

		ee()->cp->render('utilities/translate/list', $vars);
	}

	/**
	 * Zip and send the selected language files
	 *
	 * @param string $language	The language directory (i.e. 'english')
	 * @param array  $files		The list of files to export
	 */
	private function export($language, $files)
	{
		if (empty($files))
		{
			ee()->view->set_message('issue', lang('no_files_selected'));
			return;
		}

		$path = APPPATH . 'language/' . $language . '/';

		// Confirm the files exist
		foreach($files as $file)
		{
			if ( ! is_readable($path . $file . '_lang.php'))
			{
				$message = $path . $file . '_lang.php ' . lang('cannot_access') . '.';
				ee()->view->set_message('issue', $message);
				return;
			}
		}

		$tmpfilename = tempnam('', '');
		$zip = new ZipArchive();
		if ($zip->open($tmpfilename, ZipArchive::CREATE) !== TRUE)
		{
			ee()->view->set_message('issue', lang('cannot_create_zip'));
			return;
		}

		foreach($files as $file)
		{
			$zip->addFile($path . $file . '_lang.php', $file . '_lang.php');
		}
		$zip->close();

		$data = file_get_contents($tmpfilename);
		unlink($tmpfilename);

		ee()->load->helper('download');
		force_download('ExpressionEngine-language-export-' . $language . '.zip', $data);
		exit;
	}

	private function edit($language, $file)
	{
		$file = ee()->security->sanitize_filename($file);

		$path = APPPATH . 'language/' . $language . '/';
		$filename =  $file . '_lang.php';

		if ( ! is_readable($path . $filename))
		{
			$message = $path . $file . '_lang.php ' . lang('cannot_access') . '.';
			ee()->view->set_message('issue', $message, '', TRUE);
			ee()->functions->redirect(cp_url('utilities/translate/' . $language));
		}

		ee()->view->cp_page_title = $filename . ' ' . ucfirst(lang('translation'));

		$vars['language'] = $language;
		$vars['filename'] = $filename;

		$dest_dir = APPPATH . 'translations/';

		require($path . $filename);

		$M = $lang;

		unset($lang);

		if (file_exists($dest_dir . $filename))
		{
			require($dest_dir . $filename);
		}
		else
		{
			$lang = $M;
		}

		$keys = array();

		foreach ($M as $key => $val)
		{
			if ($key != '')
			{
				$trans = ( ! isset($lang[$key])) ? '' : $lang[$key];
				$keys[$key]['original'] = $val;
				$keys[$key]['trans'] = $trans;
			}
		}

		$vars = array(
			'language'  => $language,
			'file'		=> $file,
			'keys'		=> $keys
		);

		ee()->view->cp_breadcrumbs = array(
			cp_url('utilities/translate/' . $language) => ucfirst($language) . ' ' . lang('language_files')
		);

		ee()->cp->render('utilities/translate/edit', $vars);
	}

	private function save($language, $file)
	{
		$file = ee()->security->sanitize_filename($file);

		$dest_dir = APPPATH . 'translations/';
		$filename =  $file . '_lang.php';
		$dest_loc = $dest_dir . $filename;

		$str = '<?php'."\n".'$lang = array('."\n\n\n";

		foreach ($_POST as $key => $val)
		{
			$val = str_replace('<script', '', $val);
			$val = str_replace('<iframe', '', $val);
			$val = str_replace(array("\\", "'"), array("\\\\", "\'"), $val);

			$str .= '\''.$key.'\' => '."\n".'\''.$val.'\''.",\n\n";
		}

		$str .= "''=>''\n);\n\n";
		$str .= "// End of File";

		// Make sure any existing file is writeable
		if (file_exists($dest_loc))
		{
			@chmod($dest_loc, FILE_WRITE_MODE);

			if ( ! is_really_writable($dest_loc))
			{
				exit($dest_loc);
				ee()->view->set_message('issue', lang('trans_file_not_writable'), '', TRUE);
				ee()->functions->redirect(cp_url('utilities/translate/' . $language . '/edit/' . $file));
			}
		}

		$this->load->helper('file');

		if (write_file($dest_loc, $str))
		{
			ee()->view->set_message('success', lang('translations_saved'), str_replace('%s', $filename, lang('file_saved')), TRUE);
		}
		else
		{
			ee()->view->set_message('issue', lang('invalid_path'), '', TRUE);
		}
		ee()->functions->redirect(cp_url('utilities/translate/' . $language . '/edit/' . $file));
	}
}
// END CLASS

/* End of file Translate.php */
/* Location: ./system/expressionengine/controllers/cp/Utilities/Translate.php */
