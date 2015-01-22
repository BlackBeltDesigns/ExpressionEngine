<?php

namespace EllisLab\Tests\ExpressionEngine\Service\Model;

use Mockery as m;
use EllisLab\ExpressionEngine\Service\Model\Model;

class ModelTest extends \PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}

	public function testMetaData()
	{
		$m1 = new ModelStub();
		$this->assertEquals('id', $m1->getPrimaryKey());
	}

	public function testConstruct()
	{
		$m1 = new ModelStub();
		$this->assertNull($m1->id);
		$this->assertFalse($m1->isDirty());

		$m2 = new ModelStub(array('id' => 5));
		$this->assertEquals(5, $m2->id);
		$this->assertTrue($m2->isDirty());
	}

	public function testFill()
	{
		$m1 = new ModelStub();
		$this->assertFalse($m1->isDirty());

		$m1->fill(array('id' => 5, 'name' => 'Max'));

		$this->assertFalse($m1->isDirty());
		$this->assertEquals(5, $m1->id);
		$this->assertEquals('Max', $m1->name);
	}

	public function testIsNew()
	{
		$m1 = new ModelStub;
		$this->assertTrue($m1->isNew());

		$m2 = new ModelStub;
		$m2->id = 5;
		$this->assertFalse($m2->isNew());

		$m3 = new ModelStub(array('id' => 5));
		$this->assertFalse($m3->isNew());
	}

	public function testDirty()
	{
		$m1 = new ModelStub(array('id' => 5));
		$this->assertTrue($m1->isDirty());

		$m2 = new ModelStub;
		$this->assertFalse($m2->isDirty());

		$m2->fill(array('name' => 'John'));
		$this->assertFalse($m2->isDirty());

		$m2->id = 5;
		$m2->surname = 'Bates';

		$this->assertTrue($m2->isDirty());
		$this->assertEquals(
			array('id' => 5, 'surname' => 'Bates'),
			$m2->getDirty()
		);
	}

	public function testName()
	{
		$m1 = new ModelStub();
		$this->assertNull($m1->getName());

		$m1->setName('stub');
		$this->assertEquals('stub', $m1->getName());
	}

	public function testSetMutator()
	{
		$m1 = new ModelStub();

		$m1->full_name = 'Sybill Crawley';

		$this->assertEquals('Sybill', $m1->name);
		$this->assertEquals('Crawley', $m1->surname);

		$this->assertTrue($m1->isDirty());
	}

	public function testGetMutator()
	{
		$m2 = new ModelStub();

		$m2->name = 'Sybill';
		$m2->surname = 'Branson';

		$this->assertEquals('Sybill Branson', $m2->full_name);
	}

	public function testSaveInsert()
	{
		$fe = m::mock('EllisLab\ExpressionEngine\Service\Model\Frontend');
		$qb = m::mock('EllisLab\ExpressionEngine\Service\Model\Query\Builder');

		$fe->shouldReceive('get')->andReturn($qb);

		$qb->shouldReceive('set')->with(array('id' => NULL, 'name' => 'Robert', 'surname' => NULL, 'full_name' => 'Robert '));
		$qb->shouldReceive('insert')->andReturn(8);

		$m = new ModelStub();
		$m->name = 'Robert';

		// pre insert assertion
		$this->assertTrue($m->isNew());
		$this->assertTrue($m->isDirty());

		$m->setFrontend($fe);
		$m->save();

		// post insert assertions
		$this->assertFalse($m->isNew());
		$this->assertFalse($m->isDirty());
		$this->assertEquals(8, $m->id);
	}

	public function testSaveUpdate()
	{
		$fe = m::mock('EllisLab\ExpressionEngine\Service\Model\Frontend');
		$qb = m::mock('EllisLab\ExpressionEngine\Service\Model\Query\Builder');

		$fe->shouldReceive('get')->andReturn($qb);

		$qb->shouldReceive('filter')->with('id', 5);
		$qb->shouldReceive('set')->with(array('name' => 'Robert'));
		$qb->shouldReceive('update');

		$m = new ModelStub();
		$m->fill(array(
			'id' => 5,
			'name' => 'Tom'
		));

		// pre insert assertion
		$this->assertEquals('Tom', $m->name);
		$this->assertFalse($m->isNew());

		$m->name = 'Robert';
		$m->setFrontend($fe);
		$m->save();

		// post insert assertions
		$this->assertFalse($m->isNew());
		$this->assertFalse($m->isDirty());
		$this->assertEquals('Robert', $m->name);
	}

	public function testDelete()
	{
		$fe = m::mock('EllisLab\ExpressionEngine\Service\Model\Frontend');
		$qb = m::mock('EllisLab\ExpressionEngine\Service\Model\Query\Builder');

		$fe->shouldReceive('get')->andReturn($qb);
		$qb->shouldReceive('filter')->with('id', 5);
		$qb->shouldReceive('delete');

		$m = new ModelStub();
		$m->id = 5;

		// pre delete assertion
		$this->assertFalse($m->isNew());

		$m->setFrontend($fe);
		$m->delete();

		// post delete assertions
		$this->assertTrue($m->isNew());
		$this->assertFalse($m->isDirty());
	}
}

class ModelStub extends Model {

	protected static $_primary_key = 'id';

	protected $id;
	protected $name;
	protected $surname;
	protected $full_name;

	public function get__full_name()
	{
		return $this->name.' '.$this->surname;
	}

	public function set__full_name($value)
	{
		list($this->name, $this->surname) = explode(' ', $value);

		$this->markAsDirty($this->name);
		$this->markAsDirty($this->surname);
	}
}