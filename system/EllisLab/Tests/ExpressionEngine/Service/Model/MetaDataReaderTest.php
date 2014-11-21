<?php

// This file contains multiple namespaces as part of testing
// that the gateway is loaded from .\gateway\<class>
namespace EllisLab\Tests\ExpressionEngine\Service\Model {

	use Mockery as m;
	use EllisLab\ExpressionEngine\Service\Model\Model;
	use EllisLab\ExpressionEngine\Service\Model\Gateway;
	use EllisLab\ExpressionEngine\Service\Model\MetaDataReader;

	class MetaDataReaderTest extends \PHPUnit_Framework_TestCase {

		public function setUp()
		{
			$this->model_class = __NAMESPACE__.'\\MetaDataModelStub';
			$this->reader = new MetaDataReader('Stub', $this->model_class);
		}

		public function tearDown()
		{
			$this->reader = NULL;
			m::close();
		}


		public function testGetName()
		{
			$this->assertEquals('Stub', $this->reader->getName());
		}

		public function testGetClass()
		{
			$this->assertEquals($this->model_class, $this->reader->getClass());
		}

		public function testGetPrimaryKey()
		{
			$this->assertEquals('stub_id', $this->reader->getPrimaryKey());
		}

		public function testGetGateways()
		{
			$actual = $this->reader->getGateways();
			$expected = array(
				'MetaDataTestStubGateway' => __NAMESPACE__.'\\Gateway\\MetaDataTestStubGateway'
			);

			$this->assertEquals($expected, $actual);
		}

		public function testGetTables()
		{
			$actual = $this->reader->getTables();
			$expected = array(
				'stub_table' => array(
					'stub_id',
					'first_name',
					'last_name',
					'age'
				)
			);

			$this->assertEquals($expected, $actual);
		}

		public function getValidationRules()
		{

		}

		public function testGetRelationships()
		{

		}
	}

	class MetaDataModelStub extends Model {

		protected static $_primary_key = 'stub_id';
		protected static $_gateway_names = array('MetaDataTestStubGateway');

		protected static $_relationships = array(
			'Site' => array(
				'type' => 'belongsTo',
			),
			'TemplateGroup'	=> array(
				'type' => 'hasMany'
			),
			'LastAuthor' => array(
				'type'	=> 'hasOne',
				'model'	=> 'Member',
				'key'	=> 'last_author_id'
			),
			'NoAccess' => array(
				'type' => 'hasAndBelongsToMany',
				'model' => 'MemberGroup'
			)
		);

		protected $stub_id;
		protected $first_name;
		protected $last_name;
		protected $age;

	}
}

namespace EllisLab\Tests\ExpressionEngine\Service\Model\Gateway {

	use EllisLab\ExpressionEngine\Service\Model\Gateway;

	class MetaDataTestStubGateway extends Gateway {

		protected static $_table_name = 'stub_table';

		protected $stub_id;
		protected $first_name;
		protected $last_name;
		protected $age;

	}
}
// EOF