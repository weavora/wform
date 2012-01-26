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

	// @todo why $name is public?
	public $name = null;

	// @todo rename to $_info, $_model, $_type
	protected $info = null;
	protected $model = null;
	protected $type = null;

	public static function getInstance($model, $relationName, $options = array()) {
		// for 'relations' => array('someRelation','someOtherRelation')
		if (is_numeric($relationName) && is_string($options)) {
			$relationName = $options;
			$options = array();
		}

		$relationInfo = self::getRelationInfo($model, $relationName);
		if ($relationInfo === null)
			return null;

		switch($relationInfo[self::RELATION_TYPE]) {
			case CActiveRecord::HAS_ONE: $relation = new WFormRelationHasOne(); break;
			case CActiveRecord::HAS_MANY: $relation = new WFormRelationHasMany(); break;
			case CActiveRecord::BELONGS_TO: $relation = new WFormRelationBelongsTo(); break;
			case CActiveRecord::MANY_MANY: $relation = new WFormRelationManyMany(); break;
			default:
				return null;

		}

		$relation->setModel($model);
		$relation->setInfo($relationInfo);
		$relation->name = $relationName;

//		$options['model'] = $model;
//		$options['name'] = $relationName;
//		$options['info'] = $relationInfo;

		$relation->setOptions($options);

		return $relation;
	}

	public function __construct($options = array()) {
		$this->setOptions($options);
	}

	public function setOptions($options) {
		foreach($options as $key => $value) {
			if (property_exists($this, $key) && !in_array($key, array('name', 'type', 'info', 'model'))) {
				$this->{$key} = $value;
			}
		}
	}

	public static function getRelationInfo($model, $relationName) {
		$relations = $model->relations();
		if (!array_key_exists($relationName, $relations))
			return null;
		return $relations[$relationName];
	}

//	public function getInfo() {
//		return $this->info;
//	}
//
	public function setInfo($info) {
		$this->info = $info;
	}

//	public function getType() {
//		return $this->type;
//	}

	public function setType($type) {
		$this->type = $type;
	}

//	public function getModel() {
//		return $this->model;
//	}
//
	public function setModel($model) {
		$this->model = $model;
	}
}