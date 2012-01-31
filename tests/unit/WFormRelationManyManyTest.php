<?php

class WFormRelationManyManyTest extends PHPUnit_Framework_TestCase
{
	private $_connection;


	protected function setUp()
	{
		if(!extension_loaded('pdo') || !extension_loaded('pdo_sqlite'))
			$this->markTestSkipped('PDO and SQLite extensions are required.');

		$this->_connection = new CDbConnection('sqlite::memory:');
		$this->_connection->active = true;
		$this->_connection->pdoInstance->exec(file_get_contents(dirname(__FILE__).'/../fixtures/data/sqlite.sql'));
		CActiveRecord::$db = $this->_connection;
	}


	protected function tearDown()
	{
		$this->_connection->active=false;
	}

	/**
	 * @covers WFormRelationManyMany::setAttributes
	 */
	public function testSetAttributes()
	{
		$product = $this->_getProductWithRelation();

		$product->attributes = array(
			'name' => 'name',
			'tags' => array(
				array(
					'name' => 'tag_name',
				),
				array(
					'name' => 'tag_name2',
				),
			),
		);

		$this->assertCount(2, $product->tags);
		$this->assertEquals('tag_name', $product->tags[0]->name);
		$this->assertEquals('tag_name2', $product->tags[1]->name);

		$product->attributes = array(
			'name' => 'name',
		);
		$this->assertCount(2, $product->tags);

		$product->attributes = array(
			'name' => 'name',
			'tags' => array(),
		);
		$this->assertCount(0, $product->tags);

		$product = $this->_getProductWithRelation(1);

		$product->attributes = array(
			'name' => 'name',
			'tags' => array(
				array(
					'id' => 1,
				),
			),
		);

		$this->assertCount(1, $product->tags);
		// check if exists tags just updated
		$this->assertEquals('bad', $product->tags[0]->name);
	}

	/**
	 * @covers WFormRelationManyMany::validate
	 * @dataProvider validateProvider
	 */
	public function testValidate($expectedResult, $relationOptions, $relationAttribute, $onFailComment = "")
	{
		$product = $this->_getProductWithRelation(null, $relationOptions);

		$product->attributes = array(
			'name' => 'name',
			'tags' => $relationAttribute,
		);

		$this->assertEquals($expectedResult, $product->validate(), $onFailComment);
	}

	/**
	 * @covers WFormRelationManyMany::validate
	 */
	public function testUnsetInvalid()
	{
		$product = $this->_getProductWithRelation(null, array('unsetInvalid' => true));

		$product->attributes = array(
			'name' => 'name',
			'tags' => array(
				array(
					'name' => 'tag_name4'
				),
				array(
					'name' => ''
				),
			),
		);

		$this->assertTrue($product->validate());
		$this->assertCount(1, $product->tags);
	}

	public function validateProvider()
	{
		return array(
			// required=true
			array(
				'result' => true,
				'relationOptions' => array('required' => true),
				'relationAttribute' => array(
					array(
						'name' => 'tag_name'
					),
				),
				'comment' => 'required, 1 valid related object'
			),
			array(
				'result' => false,
				'relationOptions' => array('required' => true),
				'relationAttribute' => array(
					array(
						'name' => ''
					),
				),
				'comment' => 'required, 1 invalid related object'
			),
			array(
				'result' => false,
				'relationOptions' => array('required' => true),
				'relationAttribute' => array(),
				'comment' => 'required, 0 related objects'
			),
			// required=false
			array(
				'result' => true,
				'relationOptions' => array('required' => false),
				'relationAttribute' => array(
					array(
						'name' => 'tag_name'
					),
				),
				'comment' => 'not required, 1 valid related object'
			),
			array(
				'result' => false,
				'relationOptions' => array('required' => false),
				'relationAttribute' => array(
					array(
						'name' => ''
					),
				),
				'comment' => 'not required, 1 invalid related objects'
			),
			array(
				'result' => true,
				'relationOptions' => array('required' => false),
				'relationAttribute' => array(),
				'comment' => 'not required, 0 related objects'
			),
			// 'unsetInvalid' => true
			array(
				'result' => true,
				'relationOptions' => array('required' => false, 'unsetInvalid' => true),
				'relationAttribute' => array(
					array(
						'name' => ''
					),
				),
				'comment' => 'unsetInvalid, not required, 1 invalid related objects'
			),
			array(
				'result' => true,
				'relationOptions' => array('required' => true, 'unsetInvalid' => true),
				'relationAttribute' => array(
					array(
						'name' => ''
					),
				),
				'comment' => 'unsetInvalid, required, 1 invalid related objects'
			),
		);
	}

	/**
	 * @covers WFormRelationManyMany::save
	 * @dataProvider saveProvider
	 */
	public function testSave($expectedResult, $relationOptions, $relationAttribute, $onFailComment = "")
	{
		$product = $this->_getProductWithRelation(null, $relationOptions);

		$product->attributes = array(
			'name' => 'name',
			'tags' => $relationAttribute,
		);

		$this->assertEquals($expectedResult['saved'], $product->save(), $onFailComment);
		$this->assertCount($expectedResult['relationsCount'], $product->tags, $onFailComment);

		$product = $this->_getProductWithRelation(1, $relationOptions);

		$product->attributes = array(
			'name' => 'name',
			'tags' => $relationAttribute,
		);

		$this->assertEquals($expectedResult['saved'], $product->save(), $onFailComment);
		$this->assertCount($expectedResult['relationsCount'], $product->tags, $onFailComment);
	}

	public function saveProvider()
	{
		return array(
			// required=true
			array(
				'result' => array('saved' => true, 'relationsCount' => 1),
				'relationOptions' => array('required' => true),
				'relationAttribute' => array(
					array(
						'name' => 'tag_name'
					),
				),
				'comment' => 'required, 1 valid related object'
			),
			array(
				'result' => array('saved' => false, 'relationsCount' => 1),
				'relationOptions' => array('required' => true),
				'relationAttribute' => array(
					array(
						'name' => ''
					),
				),
				'comment' => 'required, 1 invalid related object'
			),
			array(
				'result' => array('saved' => false, 'relationsCount' => 0),
				'relationOptions' => array('required' => true),
				'relationAttribute' => array(),
				'comment' => 'required, 0 related objects'
			),
			// required=false
			array(
				'result' => array('saved' => true, 'relationsCount' => 1),
				'relationOptions' => array('required' => false),
				'relationAttribute' => array(
					array(
						'name' => 'tag_name'
					),
				),
				'comment' => 'not required, 1 valid related object'
			),
			array(
				'result' => array('saved' => false, 'relationsCount' => 1),
				'relationOptions' => array('required' => false),
				'relationAttribute' => array(
					array(
						'name' => ''
					),
				),
				'comment' => 'not required, 1 invalid related objects'
			),
			array(
				'result' => array('saved' => true, 'relationsCount' => 0),
				'relationOptions' => array('required' => false),
				'relationAttribute' => array(),
				'comment' => 'not required, 0 related objects'
			),
			// 'unsetInvalid' => true
			array(
				'result' => array('saved' => true, 'relationsCount' => 0),
				'relationOptions' => array('required' => false, 'unsetInvalid' => true),
				'relationAttribute' => array(
					array(
						'name' => ''
					),
				),
				'comment' => 'unsetInvalid, not required, 1 invalid related objects'
			),
			array(
				'result' => array('saved' => true, 'relationsCount' => 0),
				'relationOptions' => array('required' => true, 'unsetInvalid' => true),
				'relationAttribute' => array(
					array(
						'name' => ''
					),
				),
				'comment' => 'unsetInvalid, required, 1 invalid related objects'
			),
		);
	}

	/**
	 * @covers WFormRelationManyMany::save
	 */
	public function testSaveIfNotSet()
	{
		$product = Product::model() ;
		$product->attachBehavior('wform', array(
			'class' => 'WFormBehavior',
			'relations' => array(
				'tags' => array('required' => false),
			),
		));
		$product = $product->findByPk(1);
		$product->attachBehavior('wform', array(
			'class' => 'WFormBehavior',
			'relations' => array(
				'tags' => array('required' => false),
			),
		));
		$product->afterFind(new CEvent($product));


		$this->assertEquals(true, $product->save());
		$this->assertCount(2, $product->tags);

		$product = Product::model();
		$product->attachBehavior('wform', array(
			'class' => 'WFormBehavior',
			'relations' => array(
				'tags' => array('required' => false),
			),
		));
		$product = $product->with('tags')->findByPk(1);
		$product->attachBehavior('wform', array(
			'class' => 'WFormBehavior',
			'relations' => array(
				'tags' => array('required' => false),
			),
		));
		$product->afterFind(new CEvent($product));

		$this->assertEquals(true, $product->save());
		$this->assertCount(0, $product->tags);
	}

	/**
	 * @covers WFormRelationManyMany::getRelatedModels
	 */
	public function testGetRelatedModels()
	{
		$product = $this->_getProductWithRelation();
		$relation = WFormRelation::getInstance($product, 'tags');

		$this->assertCount(0, $relation->getRelatedModels());

		$product->attributes = array(
			'name' => 'name',
			'tags' => array(
				array(
					'id' => 1,
					'name' => 'tag_name'
				),
			),
		);

		$this->assertCount(1, $relation->getRelatedModels());

		$product = $this->_getProductWithRelation(1);
		$relation = WFormRelation::getInstance($product, 'tags');

		$this->assertCount(2, $relation->getRelatedModels());
	}
	
	/**
	 * @covers WFormRelationManyMany::getActualRelatedModels
	 */
	public function testGetActualRelatedModels()
	{
		$product = $this->_getProductWithRelation(1);
		$relation = WFormRelation::getInstance($product, 'tags');

		$this->assertCount(2, $relation->getRelatedModels());
		$this->assertCount(2, $relation->getActualRelatedModels());

		$product->attributes = array(
			'name' => 'name',
			'tags' => array(),
		);

		$this->assertCount(2, $relation->getActualRelatedModels());
		$this->assertCount(0, $relation->getRelatedModels());

		$product->attributes = array(
			'name' => 'name',
			'tags' => array(
				array(
					'name' => 'tag_name1',
				),
			),
		);

		$this->assertCount(2, $relation->getActualRelatedModels());
		$this->assertCount(1, $relation->getRelatedModels());

	}

	/**
	 * WFormRelationManyMany::lazyDelete
	 * @dataProvider lazyDeleteProvider
	 */
	public function testLazyDelete($expectedResult, $relationOptions, $relationAttribute, $onFailComment = "")
	{
		$product = $this->_getProductWithRelation(1, $relationOptions);

		$product->attributes = array(
			'name' => 'name',
			'tags' => $relationAttribute,
		);

		$this->assertCount($expectedResult['relationsCount'], $product->tags, $onFailComment);

		$product->save();

		$this->assertCount($expectedResult['relationsCount'], $product->tags, $onFailComment);
		$this->assertCount($expectedResult['relationsCount'], $product->getRelated('tags', true), $onFailComment);

		$relatedIds = array();
		foreach($product->getRelated('tags', true) as $model) {
			$relatedIds[] = $model->primaryKey;
		}

		$this->assertTrue(in_array($expectedResult['oldId'], $relatedIds) == $expectedResult['containsOld']);
	}

	public function lazyDeleteProvider()
	{
		return array(
			// required=true
			array(
				'result' => array('relationsCount' => 1, 'oldId' => 1, 'containsOld' => false),
				'relationOptions' => array('required' => false),
				'relationAttribute' => array(
					array(
						'name' => 'tag_name'
					),
				),
				'comment' => '1 new file'
			),
			array(
				'result' => array('relationsCount' => 2, 'oldId' => 1, 'containsOld' => true),
				'relationOptions' => array('required' => false),
				'relationAttribute' => array(
					array(
						'id' => 1,
						'name' => 'tag_name1'
					),
					array(
						'name' => 'tag_name2'
					),
				),
				'comment' => '1 old, 1 new file'
			),
			array(
				'result' => array('relationsCount' => 2, 'oldId' => 1, 'containsOld' => false),
				'relationOptions' => array('required' => false),
				'relationAttribute' => array(
					array(
						'name' => 'tag_name1'
					),
					array(
						'name' => 'tag_name2'
					),
				),
				'comment' => '2 new files'
			),
			array(
				'result' => array('relationsCount' => 0, 'oldId' => 1, 'containsOld' => false),
				'relationOptions' => array('required' => false),
				'relationAttribute' => array(
				),
				'comment' => '0 files'
			),
		);
	}

	/**
	 * WFormRelationManyMany::delete
	 */
	public function testDelete()
	{
		$product = $this->_getProductWithRelation(1);

		$this->assertCount(2, $product->tags);


		$this->assertTrue($product->delete());

		$sql = "SELECT COUNT(*) FROM products_2_tags WHERE product_id = 1";

		$command = $this->_connection->createCommand($sql);
		$this->assertEquals(0, $command->queryScalar());
	}

	public function testDeleteWithoutCascade()
	{
		$product = $this->_getProductWithRelation(1, array('cascadeDelete' => false));

		$this->assertCount(2, $product->tags);


		$this->assertTrue($product->delete());

		$sql = "SELECT COUNT(*) FROM products_2_tags WHERE product_id = 1";

		$command = $this->_connection->createCommand($sql);
		$this->assertEquals(2, $command->queryScalar());
	}

	/**
	 * @param null $id
	 * @param array $relationOptions
	 * @return Product
	 */
	protected function _getProductWithRelation($id = null, $relationOptions = array())
	{
		$product = $id ? Product::model()->findByPk($id) : new Product();
		$product->attachBehavior('wform', array(
			'class' => 'WFormBehavior',
			'relations' => array(
				'tags' => $relationOptions,
			),
		));
		$product->afterConstruct(new CEvent($product));

		return $product;
	}
}