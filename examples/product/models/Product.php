<?php

/**
 * This is the model class for table "products".
 *
 * The followings are the available columns in table 'products':
 * @property string $id
 * @property string $name
 * @property double $price
 */
class Product extends WActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Product the static model class
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
		return 'products';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('price, category_id', 'numerical'),
			array('name', 'required'),
			array('name', 'length', 'max'=>200),
			array('id, name, price', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
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

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'price' => 'Price',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('price',$this->price);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
