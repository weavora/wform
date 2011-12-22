<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WRelatedModelHasOne {

	public $type = CActiveRecord::HAS_ONE;
	public $relationName;
	public $relationInfo;
	public $model;

	public function setAttributes($attributes) {
		$this->model->{$this->relationName}->attributes = $attributes;
	}

	public function validate() {
		return $this->model->{$this->relationName}->validate();
	}

	public function save() {
		$foreignKey = $this->relationInfo[WRelatedModel::RELATION_FOREIGN_KEY];
		$this->model->{$this->relationName}->$foreignKey = $this->model->primaryKey;
		
		return $this->model->{$this->relationName}->save();
	}


}
