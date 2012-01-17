<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class ProductForm extends Product
{

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function relations()
	{
		return array_merge(parent::relations(), array(
			'images' => array(self::HAS_MANY, 'AttachmentForm', 'object_id', 'condition' => 'images.object_type=:object_type', 'params' => array('object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE)),
			'certificate' => array(self::HAS_ONE, 'CertificateForm', 'product_id'),
		));
	}


	public function behaviors()
	{
		return array_merge(
			parent::behaviors(),
			array(
				'wform' => array(
					'class' => 'ext.wform.WFormBehavior',
					'relations' => array(
						'category' => array('unsetInvalid' => true, 'required' => false),
						'tags' => array('required' => false),
						'images',
						'certificate',
						'description',
					),
				),
			)
		);
	}
}
