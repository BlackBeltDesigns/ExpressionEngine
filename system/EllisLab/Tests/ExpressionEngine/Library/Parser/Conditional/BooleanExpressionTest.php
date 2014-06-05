<?php

namespace EllisLab\Tests\ExpressionEngine\Library\Parser\Conditional;

use EllisLab\ExpressionEngine\Library\Parser\Conditional\BooleanExpression;
use EllisLab\ExpressionEngine\Library\Parser\Conditional\Token;

class BooleanExpressionTest extends \PHPUnit_Framework_TestCase {

	private $expr;

	public function setUp()
	{
		$this->expr = new BooleanExpression();
	}

	/**
	 * @dataProvider truthyDataProvider
	 */
	public function testTruthy($token)
	{
		$this->expr->add($token);
		$this->assertTrue($this->expr->evaluate());
	}

	/**
	 * @dataProvider falseyDataProvider
	 */
	public function testFalsey($token)
	{
		$this->expr->add($token);
		$this->assertFalse($this->expr->evaluate());
	}

	public function truthyDataProvider()
	{
		return array(
			array(new Token\String('ee')),
			array(new Token\String('0')),
			array(new Token\Number(1)),
			array(new Token\Number(0.001)),
			array(new Token\Bool('TRUE')),
		);
	}

	public function falseyDataProvider()
	{
		return array(
			array(new Token\String('')),
			array(new Token\Number(0)),
			array(new Token\Number(0.0)),
			array(new Token\Bool('FALSE')),
		);
	}
}