<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WRelatedModelHasOne {

	public $type;
	public $relationName;
	public $relationInfo;
	public $model;

	public function __construct() {
		$this->type = CActiveRecord::HAS_ONE;
	}

	public function initRelation() {
		$relationClass = $this->relationInfo[WRelatedModel::RELATION_CLASS];
		$relationName = $this->relationName;
		$this->model->$relationName = new $relationClass();
	}

	public function setAttributes($attributes) {
		$relationName = $this->relationName;
		$this->model->$relationName->attributes = $attributes;
	}

	public function validate() {
		$relationName = $this->relationName;
		return $this->model->$relationName->validate();
	}

	public function save() {
		$relationName = $this->relationName;
		$foreignKey = $this->relationInfo[WRelatedModel::RELATION_FOREIGN_KEY];
		$this->model->$relationName->$foreignKey = $this->model->id;
		return $this->model->$relationName->save();
	}


}
