<?php

/**
 * This is the model class for table "attachments".
 *
 * The followings are the available columns in table 'attachments':
 * @property string $id
 * @property integer $object_id
 * @property string $object_type
 * @property string $file
 * @property string $file_origin
 */
class Attachment extends WActiveRecord
{
	const OBJECT_TYPE_PRODUCT_IMAGE = 'product_image';
	const OBJECT_TYPE_CERTIFICATE = 'certificate';

	/**
	 * Returns the static model of the specified AR class.
	 * @return Attachment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'attachments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('object_id', 'numerical', 'integerOnly'=>true),
			array('object_type', 'length', 'max'=>13),
			array('file, file_origin', 'length', 'max'=>250),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, object_id, object_type, file, file_origin', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
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

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id,true);
		$criteria->compare('object_id',$this->object_id);
		$criteria->compare('object_type',$this->object_type,true);
		$criteria->compare('file',$this->file,true);
		$criteria->compare('file_origin',$this->file_origin,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

		public function getFilePath() {
		return Yii::app()->runtimePath.'/'.$this->object_type . '/' . $this->file;
	}

	public function getFileUrl() {
		if ($this->file) {
		    return Yii::app()->createUrl('/protected/runtime/' . $this->object_type . '/' . $this->file);
		}
	}
}
