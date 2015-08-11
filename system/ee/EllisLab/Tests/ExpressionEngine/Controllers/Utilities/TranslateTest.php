<?php
namespace EllisLab\Tests\ExpressionEngine\Controllers\Utilities;

class TranslateTest extends \PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass()
	{
		require_once(APPPATH.'core/Controller.php');
	}

	public function testRoutableMethods()
	{
		$controller_methods = array();

		foreach (get_class_methods('EllisLab\ExpressionEngine\Controller\Utilities\Translate') as $method)
		{
			$method = strtolower($method);
			if (strncmp($method, '_', 1) != 0)
			{
				$controller_methods[] = $method;
			}
		}

		sort($controller_methods);

		// This one has more routable functions due to __call(), we need to
		// test those as well @TODO
		$this->assertEquals(array('index'), $controller_methods);
	}

}