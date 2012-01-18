<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class Certificate extends WActiveRecord
{

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'certificates';
	}

	public function rules()
	{
		return array(
			array('name', 'required'),
		);
	}

	public function relations()
	{
		return array(
			'image' => array(self::HAS_ONE, 'Attachment', 'object_id', 'condition' => 'image.object_type=:object_type', 'params' => array('object_type' => Attachment::OBJECT_TYPE_CERTIFICATE)),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
		);
	}
}
