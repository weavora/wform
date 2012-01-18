<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class Attachment extends WActiveRecord
{

	const OBJECT_TYPE_PRODUCT_IMAGE = 'product_image';
	const OBJECT_TYPE_CERTIFICATE = 'certificate';

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'attachments';
	}

	public function rules()
	{
		return array(
			array('object_id', 'numerical', 'integerOnly'=>true),
			array('object_type', 'length', 'max'=>13),
			array('file, file_origin', 'length', 'max'=>250),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'object_id' => 'Object',
			'object_type' => 'Object Type',
			'file' => 'File',
			'file_origin' => 'File Origin',
		);
	}

	public function getFilePath()
	{
		return Yii::app()->runtimePath.'/'.$this->object_type . '/' . $this->file;
	}

	public function getFileUrl()
	{
		if ($this->file) {
		    return Yii::app()->createUrl('/protected/runtime/' . $this->object_type . '/' . $this->file);
		}
	}
}
