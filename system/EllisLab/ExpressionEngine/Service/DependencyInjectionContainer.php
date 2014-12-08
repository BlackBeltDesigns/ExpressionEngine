<?php

namespace EllisLab\ExpressionEngine\Service;

use Closure;
use Exception;
use EllisLab\ExpressionEngine\Service\ServiceProvider;
use EllisLab\ExpressionEngine\Service\DependencyInjectionBindingDecorator;

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
 * ExpressionEngine Dependency Injection Container
 *
 * A service to track dependencies in other services and act as a service
 * factory and instance container.
 *
 * @package		ExpressionEngine
 * @subpackage	Core
 * @category	Service
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class DependencyInjectionContainer implements ServiceProvider {

	/**
	 * @var array An associative array of registered dependencies
	 */
	protected $registry = array();

	/**
	 * @var array An associative array of singletons
	 */
	protected $singletonRegistry = array();

	/**
	 * Registers a dependency with the container
	 *
	 * @param string      $name   The name of the dependency in the form
	 *                            Vendor:Namespace
	 * @param Closure|obj $object The object to use
	 * @param array       $registry Which registry are we acting on?
	 * @return void
	 */
	private function assignToRegistry($name, $object, &$registry)
	{
		if (strpos($name, ':') === FALSE)
		{
			$name = 'EllisLab:' . $name;
		}

		if ( isset($registry[$name]))
		{
			throw new Exception('Attempt to reregister existing class' . $name);
		}

		$registry[$name] = $object;
	}

	/**
	 * Registers a dependency with the container
	 *
	 * @param string      $name   The name of the dependency in the form
	 *                            Vendor:Namespace
	 * @param Closure|obj $object The object to use
	 * @return self Returns this DependencyInjectionContainer object
	 */
	public function register($name, $object)
	{
		$this->assignToRegistry($name, $object, $this->registry);
		return $this;
	}

	/**
	 * Temporarily bind a dependency. Calls $this->register with $temp as TRUE
	 *
	 * @param string      $name   The name of the dependency in the form
	 *                            Vendor:Namespace
	 * @param Closure|obj $object The object to use
	 * @return self Returns this DependencyInjectionContainer object
	 */
	public function bind($name, $object)
	{
		$binding_isolation = new DependencyInjectionBindingDecorator($this);
		$binding_isolation->bind($name, $object);

		return $binding_isolation;
	}

	/**
	 * Registers a singleton dependency with the container
	 *
	 * @param string      $name   The name of the dependency in the form
	 *                            Vendor:Namespace
	 * @param Closure|obj $object The object to use
	 * @return self Returns this DependencyInjectionContainer object
	 */
	public function registerSingleton($name, $object)
	{
		if ($object instanceof Closure)
		{
			return $this->register($name, function($di) use ($object)
				{
					return $di->singleton($object);
				});
		}

		return $this->register($name, $object);
	}

	/**
	 * This will exectute the provided Closure exactly once, storing the result
	 * of the execution in an array and always returning that array element.
	 *
	 * @param Closure $object The Closure to execute
	 * @return mixed The result of the Closure $object
	 */
	public function singleton(Closure $object)
	{
	    $hash = spl_object_hash($object);

	    if ( ! isset($this->singletonRegistry[$hash]))
	    {
	        $this->singletonRegistry[$hash] = $object($this);
	    }

	    return $this->singletonRegistry[$hash];
	}

	/**
	 * Make an instance of a Service
	 *
	 * Retrieves an instance of a service from the DIC using the registered
	 * callback methods.
	 *
	 * @param	string	$name	The name of the registered service to be retrieved
	 * 		in format 'Vendor/Module:Namespace\Class'.
	 *
	 * @param	...	(Optional) Any additional arguments the service needs on
	 * 		initialization.
	 *
	 * @throws	RuntimeException	On attempts to access a service that hasn't
	 * 		been registered, will throw a RuntimeException.
	 *
	 * @return	Object	An instance of the service being requested.
	 */
	public function make()
	{
		$arguments = func_get_args();

		$di = $this;
		$name = array_shift($arguments);

		if ($name instanceof DependencyInjectionBindingDecorator)
		{
			$di = $name;
			$name = array_shift($arguments);
		}

		if (strpos($name, ':') === FALSE)
		{
			$name = 'EllisLab:' . $name;
		}

		if ( ! isset($this->registry[$name]))
		{
			throw new \RuntimeException('Attempt to access unregistered service ' . $name . ' in the DIC.');
		}
		else
		{
			$object = $this->registry[$name];
		}

		if ($object instanceof Closure)
		{
			array_unshift($arguments, $di);
			return call_user_func_array($object, $arguments);
		}

		return $object;
	}

}
// EOF