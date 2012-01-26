<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class CertificateForm extends Certificate
{

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function relations()
	{
		return array_merge(parent::relations(), array(
			'image' => array(self::HAS_ONE, 'AttachmentForm', 'object_id', 'condition' => 'image.object_type=:object_type', 'params' => array('object_type' => Attachment::OBJECT_TYPE_CERTIFICATE)),
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
						'image' => array(),
					),
				),
			)
		);
	}


}
