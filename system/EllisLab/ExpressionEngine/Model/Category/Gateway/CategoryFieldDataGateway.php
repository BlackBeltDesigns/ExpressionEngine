<?php
namespace EllisLab\ExpressionEngine\Model\Category\Gateway;

use EllisLab\ExpressionEngine\Model\Gateway\FieldDataGateway;

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
 * ExpressionEngine Category Field Data Table
 *
 * @package		ExpressionEngine
 * @subpackage	Category\Gateway
 * @category	Model
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class CategoryFieldDataGateway extends FieldDataGateway {

	protected static $_table_name = 'category_field_data';
	protected static $_primary_key = 'cat_id';
	protected static $_field_table = 'category_fields';
	protected static $_field_id_name = 'field_id';
	protected static $_related_gateways = array(
		'cat_id' => array(
			'gateway' => 'CategoryGateway',
			'key'	 => 'cat_id'
		),
		'site_id' => array(
			'gateway' => 'SiteGateway',
			'key'	 => 'site_id'
		),
		'group_id' => array(
			'gateway' => 'CategoryGroupGateway',
			'key'	 => 'group_id'
		),
	);

	// Properties
	protected $cat_id;
	protected $site_id;
	protected $group_id;

}
