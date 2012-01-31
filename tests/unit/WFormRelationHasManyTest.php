<?php

class WFormRelationHasManyTest extends PHPUnit_Framework_TestCase
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
	 * @covers WFormRelationHasMany::setAttributes
	 */
	public function testSetAttributes()
	{
		$product = $this->_getProductWithRelation();

		$product->attributes = array(
			'name' => 'name',
			'images' => array(
				array(
					'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
					'file' => 'somefile.txt',
				),
				array(
					'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
					'file' => 'somefile2.txt',
				),
			),
		);

		$this->assertCount(2, $product->images);
		$this->assertEquals(Attachment::OBJECT_TYPE_PRODUCT_IMAGE, $product->images[0]->object_type);
		$this->assertEquals('somefile.txt', $product->images[0]->file);

		$product->attributes = array(
			'name' => 'name',
		);
		$this->assertCount(2, $product->images);

		$product->attributes = array(
			'name' => 'name',
			'images' => array(),
		);
		$this->assertCount(0, $product->images);

		$product = $this->_getProductWithRelation(1);

		$product->attributes = array(
			'name' => 'name',
			'images' => array(
				array(
					'id' => 1,
					'file' => 'newfile.txt',
				),
			),
		);

		$this->assertCount(1, $product->images);
		// check if exists images just updated
		$this->assertEquals(Attachment::OBJECT_TYPE_PRODUCT_IMAGE, $product->images[0]->object_type);
	}

	/**
	 * @covers WFormRelationHasMany::validate
	 * @dataProvider validateProvider
	 */
	public function testValidate($expectedResult, $relationOptions, $relationAttribute, $onFailComment = "")
	{
		$product = $this->_getProductWithRelation(null, $relationOptions);

		$product->attributes = array(
			'name' => 'name',
			'images' => $relationAttribute,
		);

		$this->assertEquals($expectedResult, $product->validate(), $onFailComment);
	}

	/**
	 * @covers WFormRelationHasMany::validate
	 */
	public function testUnsetInvalid()
	{
		$product = $this->_getProductWithRelation(null, array('unsetInvalid' => true));

		$product->attributes = array(
			'name' => 'name',
			'images' => array(
				array(
					'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
					'file' => 'somefile.txt'
				),
				array(
					'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
					'file' => ''
				),
			),
		);

		$this->assertTrue($product->validate());
		$this->assertCount(1, $product->images);
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
						'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
						'file' => 'somefile.txt'
					),
				),
				'comment' => 'required, 1 valid related object'
			),
			array(
				'result' => false,
				'relationOptions' => array('required' => true),
				'relationAttribute' => array(
					array(
						'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
						'file' => ''
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
						'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
						'file' => 'somefile.txt'
					),
				),
				'comment' => 'not required, 1 valid related object'
			),
			array(
				'result' => false,
				'relationOptions' => array('required' => false),
				'relationAttribute' => array(
					array(
						'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
						'file' => ''
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
						'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
						'file' => ''
					),
				),
				'comment' => 'unsetInvalid, not required, 1 invalid related objects'
			),
			array(
				'result' => true,
				'relationOptions' => array('required' => true, 'unsetInvalid' => true),
				'relationAttribute' => array(
					array(
						'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
						'file' => ''
					),
				),
				'comment' => 'unsetInvalid, required, 1 invalid related objects'
			),
		);
	}

	/**
	 * @covers WFormRelationHasMany::save
	 * @dataProvider saveProvider
	 */
	public function testSave($expectedResult, $relationOptions, $relationAttribute, $onFailComment = "")
	{
		$product = $this->_getProductWithRelation(null, $relationOptions);

		$product->attributes = array(
			'name' => 'name',
			'images' => $relationAttribute,
		);

		$this->assertEquals($expectedResult['saved'], $product->save(), $onFailComment);
		$this->assertCount($expectedResult['relationsCount'], $product->images, $onFailComment);
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
						'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
						'file' => 'somefile.txt'
					),
				),
				'comment' => 'required, 1 valid related object'
			),
			array(
				'result' => array('saved' => false, 'relationsCount' => 1),
				'relationOptions' => array('required' => true),
				'relationAttribute' => array(
					array(
						'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
						'file' => ''
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
						'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
						'file' => 'somefile.txt'
					),
				),
				'comment' => 'not required, 1 valid related object'
			),
			array(
				'result' => array('saved' => false, 'relationsCount' => 1),
				'relationOptions' => array('required' => false),
				'relationAttribute' => array(
					array(
						'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
						'file' => ''
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
						'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
						'file' => ''
					),
				),
				'comment' => 'unsetInvalid, not required, 1 invalid related objects'
			),
			array(
				'result' => array('saved' => true, 'relationsCount' => 0),
				'relationOptions' => array('required' => true, 'unsetInvalid' => true),
				'relationAttribute' => array(
					array(
						'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
						'file' => ''
					),
				),
				'comment' => 'unsetInvalid, required, 1 invalid related objects'
			),
		);
	}

	/**
	 * @covers WFormRelationHasMany::save
	 */
	public function testSaveIfNotSet()
	{
		$product = Product::model() ;
		$product->attachBehavior('wform', array(
			'class' => 'WFormBehavior',
			'relations' => array(
				'images' => array('required' => false),
			),
		));
		$product = $product->findByPk(1);
		$product->attachBehavior('wform', array(
			'class' => 'WFormBehavior',
			'relations' => array(
				'images' => array('required' => false),
			),
		));
		$product->afterFind(new CEvent($product));


		$this->assertEquals(true, $product->save());
		$this->assertCount(1, $product->images);

		$product = Product::model();
		$product->attachBehavior('wform', array(
			'class' => 'WFormBehavior',
			'relations' => array(
				'images' => array('required' => false),
			),
		));
		$product = $product->with('images')->findByPk(1);
		$product->attachBehavior('wform', array(
			'class' => 'WFormBehavior',
			'relations' => array(
				'images' => array('required' => false),
			),
		));
		$product->afterFind(new CEvent($product));

		$this->assertEquals(true, $product->save());
		$this->assertCount(0, $product->images);
	}

	/**
	 * @covers WFormRelationHasMany::getRelatedModels
	 */
	public function testGetRelatedModels()
	{
		$product = $this->_getProductWithRelation();
		$relation = WFormRelation::getInstance($product, 'images');

		$this->assertCount(0, $relation->getRelatedModels());

		$product->attributes = array(
			'name' => 'name',
			'images' => array(
				array(
					'id' => 1,
					'file' => 'newfile.txt',
				),
			),
		);

		$this->assertCount(1, $relation->getRelatedModels());

		$product = $this->_getProductWithRelation(1);
		$relation = WFormRelation::getInstance($product, 'images');

		$this->assertCount(1, $relation->getRelatedModels());
	}

	/**
	 * @covers WFormRelationHasMany::getActualRelatedModels
	 */
	public function testGetActualRelatedModels()
	{
		$product = $this->_getProductWithRelation(1);
		$relation = WFormRelation::getInstance($product, 'images');

		$this->assertCount(1, $relation->getRelatedModels());
		$this->assertCount(1, $relation->getActualRelatedModels());

		$product->attributes = array(
			'name' => 'name',
			'images' => array(),
		);

		$this->assertCount(1, $relation->getActualRelatedModels());
		$this->assertCount(0, $relation->getRelatedModels());

		$product->attributes = array(
			'name' => 'name',
			'images' => array(
				array(
					'file' => 'newfile1.txt',
				),
				array(
					'file' => 'newfile2.txt',
				),
			),
		);

		$this->assertCount(2, $relation->getRelatedModels());
		$this->assertCount(1, $relation->getActualRelatedModels());
	}

	/**
	 * WFormRelationHasMany::lazyDelete
	 * @dataProvider lazyDeleteProvider
	 */
	public function testLazyDelete($expectedResult, $relationOptions, $relationAttribute, $onFailComment = "")
	{
		$product = $this->_getProductWithRelation(1, $relationOptions);

		$product->attributes = array(
			'name' => 'name',
			'images' => $relationAttribute,
		);

		$this->assertCount($expectedResult['relationsCount'], $product->images, $onFailComment);

		$product->save();

		$this->assertCount($expectedResult['relationsCount'], $product->images, $onFailComment);
		$this->assertCount($expectedResult['relationsCount'], $product->getRelated('images', true), $onFailComment);

		$relatedIds = array();
		foreach($product->getRelated('images', true) as $model) {
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
						'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
						'file' => 'somefile.txt'
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
						'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
						'file' => 'somefile.txt'
					),
					array(
						'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
						'file' => 'somefile2.txt'
					),
				),
				'comment' => '1 old, 1 new file'
			),
			array(
				'result' => array('relationsCount' => 2, 'oldId' => 1, 'containsOld' => false),
				'relationOptions' => array('required' => false),
				'relationAttribute' => array(
					array(
						'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
						'file' => 'somefile.txt'
					),
					array(
						'object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE,
						'file' => 'somefile.txt'
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
	 * WFormRelationHasMany::delete
	 */
	public function testDelete()
	{
		$product = $this->_getProductWithRelation(1);

		$this->assertCount(1, $product->images);
		$id = $product->images[0]->primaryKey;
		$this->assertTrue($product->delete());
		$this->assertEmpty(Attachment::model()->findByPk($id));
	}

/**
	 * WFormRelationHasMany::delete
	 */
	public function testDeleteWithoutCascade()
	{
		$product = $this->_getProductWithRelation(1, array('cascadeDelete' => false));

		$this->assertCount(1, $product->images);
		$id = $product->images[0]->primaryKey;
		$this->assertTrue($product->delete());
		$this->assertNotEmpty(Attachment::model()->findByPk($id));
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
				'images' => $relationOptions,
			),
		));
		$product->afterConstruct(new CEvent($product));

		return $product;
	}
}