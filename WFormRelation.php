<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WFormRelation {

	const RELATION_TYPE = 0;
	const RELATION_CLASS = 1;
	const RELATION_FOREIGN_KEY = 2;

	public $required = false;
	public $allowEmpty = false;
	public $unsetInvalid = false;

	public $name = null;
	protected $info = null;
	protected $model = null;
	protected $type = null;

	public static function getInstance($model, $relationName, $options = array()) {
		if (!self::_isRelationExists($model, $relationName))
			return null;

		$relationInfo = self::getRelationInfo($model, $relationName);

		switch($relationInfo[self::RELATION_TYPE]) {
			case CActiveRecord::HAS_ONE: $relation = new WFormRelationHasOne(); break;
			case CActiveRecord::HAS_MANY: $relation = new WFormRelationHasMany(); break;
			case CActiveRecord::BELONGS_TO: $relation = new WFormRelationBelongsTo(); break;
			case CActiveRecord::MANY_MANY: $relation = new WFormRelationManyMany(); break;
		}

		$options['model'] = $model;
		$options['name'] = $relationName;
		$options['info'] = $relationInfo;

		$relation->setOptions($options);

		return $relation;
	}

	public function __construct($options = array()) {
		$this->setOptions($options);
	}

	public function setOptions($options) {
		foreach($options as $key => $value) {
			if (property_exists($this, $key)) {
				$this->{$key} = $value;
			}
		}
	}

	public static function getRelationInfo($model, $relationName) {
		$relations = $model->relations();
		return $relations[$relationName];
	}

	protected static function _isRelationExists($model, $relationName) {
		$relations = $model->relations();
		return array_key_exists($relationName, $relations);
	}
}