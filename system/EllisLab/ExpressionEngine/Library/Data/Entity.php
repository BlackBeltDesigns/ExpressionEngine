<?php

namespace EllisLab\ExpressionEngine\Library\Data;

use InvalidArgumentException;
use EllisLab\ExpressionEngine\Library\Mixin\MixableImpl;

abstract class Entity extends MixableImpl {

	/**
	 * @var Array Filter storage
	 */
	protected $_filters = array();

	/**
	 * Constructor
	 */
	public function __construct(array $data = array())
	{
		foreach ($data as $key => $value)
		{
			$this->setProperty($key, $value);
		}
	}

	/**
	 *
	 */
	public function __get($name)
	{
		return $this->getProperty($name);
	}

	/**
	 *
	 */
	public function __set($name, $value)
	{
		$this->setProperty($name, $value);
		return $value;
	}

	/**
	 *
	 */
	public function __call($method, $args)
	{
		return $this->getMixinManager()->call($method, $args);
	}

	/**
	 * Access any static metadata you might need. THis automatically
	 * merges metadata for extended classes.
	 *
	 * @param String $key Name of the static property
	 * @return mixed The metadata value
	 */
	public static function getMetaData($key)
	{
		$values = static::getMetaDataByClass($key);

		if ( ! count($values))
		{
			return NULL;
		}

		$result = array_shift($values);

		foreach ($values as $class => $value)
		{
			if (is_array($result) && is_array($value))
			{
				$result = array_merge($result, $value);
			}
			else
			{
				$result = $value;
			}
		}

		return $result;
	}

	/**
	 * Access all static metadata, grouped by class name.
	 *
	 * @param String $key Metadata name
	 * @return Array [class => value] for all classes that define the metadata
	 */
	public static function getMetaDataByClass($key)
	{
		$key = '_'.$key;
		$values = array();

		$class = get_called_class();

		do
		{
			if (property_exists($class, $key))
			{
				$values[$class] = $class::$$key;
			}
		}
		while ($class = get_parent_class($class));

		return array_reverse($values);
	}

	/**
	 * The default mixin implementation is to rely on a `mixins`
	 * metadata key that contains the mixin class names.
	 */
	protected function getMixinClasses()
	{
		return $this->getMetaData('mixins') ?: array();
	}

	/**
	 * Add a filter
	 *
	 * @param String $type Filter type
	 * @param Callable $callback Filter callback
	 */
	public function addFilter($type, /*Callable */ $callback)
	{
		if ( ! array_key_exists($type, $this->_filters))
		{
			$this->_filters[$type] = array();
		}

		$this->_filters[$type][] = $callback;
	}

	/**
	 * Get all known filters of a given type
	 *
	 * @param String $type Filter type
	 * @return Array of callables
	 */
	protected function getFilters($type)
	{
		return $this->_filters[$type];
	}

	/**
	 * Apply known filters to a given value
	 *
	 * @param String $type Filter type
	 * @param Array $args List of arguments
	 * @return Filtered value
	 */
	protected function filter($type, $value, $args = array())
	{
		array_unshift($args, $value);

		foreach ($this->getFilters($type) as $filter)
		{
			$args[0] = call_user_func_array($filter, $args);
		}

		return $args[0];
	}

	/**
	 * Batch update properties
	 *
	 * Safely updates any properties that might exist,
	 * passing them through the getters along the way.
	 *
	 * @param array $data Data to update
	 * @return $this
	 */
	public function set(array $data = array())
	{
		foreach ($data as $k => $v)
		{
			if ($this->hasProperty($k))
			{
				$this->setProperty($k, $v);
			}
		}

		return $this;
	}

	/**
	 * Fill data without passing through a getter
	 *
	 * @param array $data Data to fill
	 * @return $this
	 */
	public function fill(array $data = array())
	{
		foreach ($data as $k => $v)
		{
			if ($this->hasProperty($k))
			{
				$this->$k = $v;
			}
		}

		return $this;
	}

	/**
	 * Check if the entity has a given property
	 *
	 * @param String $name Property name
	 * @return bool has property?
	 */
	public function hasProperty($name)
	{
		return (property_exists($this, $name) && $name[0] !== '_');
	}

	/**
	 * Attempt to get a property. Called by __get.
	 *
	 * @param String $name Name of the property
	 * @return Mixed Value of the property
	 */
	public function getProperty($name)
	{
		if (method_exists($this, 'get__'.$name))
		{
			return $this->{'get__'.$name}();
		}

		return $this->getRawProperty($name);
	}

	/**
	 * Attempt to set a property. Called by __set.
	 *
	 * @param String $name Name of the property
	 * @param Mixed  $value Value of the property
	 * @return $this
	 */
	public function setProperty($name, $value)
	{
		if (method_exists($this, 'set__'.$name))
		{
			$this->{'set__'.$name}($value);
		}
		else
		{
			$this->setRawProperty($name, $value);
		}

		return $this;
	}

	/**
	 * Get a property directly, bypassing the getter. This method should
	 * not be extended with additional logic, it should be treated as a
	 * way to bypass __get() and all that comes with it.
	 *
	 * @param String $name Name of the property
	 * @return Mixed $value Value of the property
	 */
	public function getRawProperty($name)
	{
		if ($this->hasProperty($name))
		{
			return $this->$name;
		}

		throw new InvalidArgumentException("No such property: '{$name}' on ".get_called_class());
	}

	/**
	 * Get a property directly, bypassing the getter. This method should
	 * not be extended with additional logic, it should be treated as a
	 * way to bypass __get() and all that comes with it.
	 *
	 * @param String $name Name of the property
	 * @param Mixed  $value Value of the property
	 * @return $this
	 */
	public function setRawProperty($name, $value)
	{
		if ($this->hasProperty($name))
		{
			$this->$name = $value;
			return $this;
		}

		throw new InvalidArgumentException("No such property: '{$name}' on ".get_called_class());
	}

	/**
	 * Get a list of fields
	 *
	 * @return array field names
	 */
	public static function getFields()
	{
		$vars = get_class_vars(get_called_class());
		$fields = array();

		foreach ($vars as $key => $value)
		{
			if ($key[0] != '_')
			{
				$fields[] = $key;
			}
		}

		return $fields;
	}

	/**
	 * Get all current values
	 *
	 * @return array Current values. Including null values - Beware.
	 */
	public function getValues()
	{
		$result = array();

		foreach ($this->getFields() as $field)
		{
			$result[$field] = $this->getProperty($field);
		}

		return $result;
	}

	/**
	 * Get all current raw values
	 *
	 * @return array Raw values, including null properties - Beware.
	 */
	public function getRawValues()
	{
		$result = array();

		foreach ($this->getFields() as $field)
		{
			$result[$field] = $this->$field;
		}

		return $result;
	}

	/**
	 * Retrieve data as an array. All getters will be hit.
	 *
	 * @return array Data including NULL values
	 */
	public function toArray()
	{
		return $this->getValues();
	}

	/**
	 * Same as `toArray()`, but retrieve data as json
	 *
	 * @return string json formatted data
	 */
	public function toJson()
	{
		return json_encode($this->toArray());
	}
}