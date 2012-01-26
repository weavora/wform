<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class ProductDescription extends WActiveRecord
{

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'product_descriptions';
	}

	public function rules()
	{
		return array(
			array('size', 'required'),
			array('color, product_id', 'safe'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'color' => 'Color',
			'size' => 'Size',
		);
	}
}
