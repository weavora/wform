<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class Product extends WActiveRecord
{

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'products';
	}

	public function rules()
	{
		return array(
			array('price, category_id', 'numerical'),
			array('name', 'required'),
		);
	}

	public function relations()
	{
		return array(
			'category' => array(self::BELONGS_TO, 'Category', 'category_id'),
			'tags' => array(self::MANY_MANY, 'Tag', 'products_2_tags(product_id, tag_id)'),
			'images' => array(self::HAS_MANY, 'Attachment', 'object_id', 'condition' => 'images.object_type=:object_type', 'params' => array('object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE)),
			'certificate' => array(self::HAS_ONE, 'Certificate', 'product_id'),
			'description' => array(self::HAS_ONE, 'ProductDescription', 'product_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'price' => 'Price',
		);
	}
}
