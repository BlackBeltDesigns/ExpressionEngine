<?php
namespace EllisLab\ExpressionEngine\Model\Addon;

use EllisLab\ExpressionEngine\Service\Model\Model;

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
 * ExpressionEngine Module Model
 *
 * @package		ExpressionEngine
 * @subpackage	Addon
 * @category	Model
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Module extends Model {

	protected static $_primary_key = 'module_id';
	protected static $_gateway_names = array('ModuleGateway');
	protected static $_validation_rules = array(
		'module_id'          => 'required',
		'has_cp_backend'     => 'enum[y,n]',
		'has_publish_fields' => 'enum[y,n]'
	);

	protected $module_id;
	protected $module_name;
	protected $module_version;
	protected $has_cp_backend;
	protected $has_publish_fields;

}
