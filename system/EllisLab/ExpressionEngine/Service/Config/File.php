<?php

namespace EllisLab\ExpressionEngine\Service\Config;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package   ExpressionEngine
 * @author    EllisLab Dev Team
 * @copyright Copyright (c) 2003 - 2014, EllisLab, Inc.
 * @license   http://ellislab.com/expressionengine/user-guide/license.html
 * @link      http://ellislab.com
 * @since     Version 3.0.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Config File Class
 *
 * @package    ExpressionEngine
 * @subpackage Core
 * @category   Core
 * @author     EllisLab Dev Team
 * @link       http://ellislab.com
 */
class File implements Config
{
	protected $config;
	protected $defaults = array(
		'database' => array(
			'active_group'     => 'expressionengine',
			'active_record'    => TRUE,
			'expressionengine' => array(
				'hostname' => 'localhost',
				'username' => 'root',
				'password' => '',
				'database' => '',
				'dbdriver' => 'mysql',
				'pconnect' => FALSE,
				'dbprefix' => 'exp_',
				'swap_pre' => 'exp_',
				'db_debug' => TRUE,
				'cache_on' => FALSE,
				'autoinit' => FALSE,
				'char_set' => 'utf8',
				'dbcollat' => 'utf8_general_ci',
				'cachedir' => '', // Set in constructor
				'stricton' => TRUE,
			)
		)
	);

	/**
	 * Create a new Config\File object, will merge with defaults
	 *
	 * @param string $path Full path to the config file
	 */
	function __construct($path)
	{
		$this->defaults['database']['expressionengine']['cachedir'] = rtrim(APPPATH, '/').'/cache/db_cache/';

		// Load in config
		require($path);
		$this->config = $config;
	}

	/**
	 * Get an item from the config, you can use
	 * "item.subitem.subsubitem" to drill down in the config
	 *
	 * @param  string $path    The config item to get
	 * @param  mixed  $default The value to return if $path can not be found
	 * @param  boolean $merge  Whether to merge with defaults if value is an
	 *                         array
	 * @return mixed           The value found for $path, otherwise $default
	 */
	public function get($path, $default = NULL, $merge = FALSE)
	{
		$config  = $this->getArrayValue($this->config, $path);
		$default = $default ?: $this->getArrayValue($this->defaults, $path);

		if ($merge && is_array($config) && is_array($default))
		{
			$config = array_replace_recursive($default, $config);
		}

		return ($config !== NULL) ? $config : $default;
	}

	/**
	 * Set an item in the config. You can use 'item.subitem.subsubitem' to drill
	 * down in the config.
	 *
	 * @param  string $path    The config item to set
	 * @param  mixed  $value   The value to set
	 * @return void
	 */
	public function set($path, $value)
	{
		// If the value is equal to the default, don't save it
		if ($value == $this->getArrayValue($this->defaults, $path))
		{
			$value = NULL;
		}

		$this->setArrayValue($this->config, $path, $value);
	}

	/**
	 * Get a nested array value given a dot-separated path
	 *
	 * @param  array  $array Array to traverse
	 * @param  string $path  Dot-separated path
	 * @return mixed         Array value
	 */
	private function getArrayValue($array, $path)
	{
		$path = explode('.', $path);

		for ($i = $array; $key = array_shift($path); $i = $i[$key])
		{
			if ( ! isset($i[$key]))
			{
				return NULL;
			}
		}

		return $i;
	}

	/**
	 * Set a nested array value given a dot-separated path
	 *
	 * @param array &$array  Array to traverse and set value in
	 * @param string $path   Dot-separated path
	 * @param mixed  $value  Value to set, pass in NULL to unset
	 */
	private function setArrayValue(&$array, $path, $value)
	{
		$path = explode('.', $path);

		for ($i = &$array; $key = array_shift($path); $i = &$i[$key])
		{
			if ( ! isset($i[$key]))
			{
				$i[$key] = array();
			}

			// Maintain a list of the last array and key for unsetting
			$last_array = &$i;
			$last_key = $key;
		}

		// Unset it if value is NULL
		if ($value === NULL)
		{
			unset($last_array[$last_key]);
		}
		else
		{
			$i = $value;
		}
	}
}
