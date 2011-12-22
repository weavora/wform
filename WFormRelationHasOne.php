<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WFormRelationHasOne {

	public $type = CActiveRecord::HAS_ONE;
	public $relationName;
	public $relationInfo;
	public $model;

	public function setAttributes($attributes) {
		$relationClass = $this->relationInfo[WFormRelation::RELATION_CLASS];
		
		if (!$this->model->{$this->relationName})
			$this->model->{$this->relationName} = new $relationClass();
		$this->model->{$this->relationName}->attributes = $attributes;
	}

	public function validate() {
		$relationClass = $this->relationInfo[WFormRelation::RELATION_CLASS];

		// @todo what if we define relation into behavior but it absent into form ?
		if (!$this->model->{$this->relationName})
			$this->model->{$this->relationName} = new $relationClass();
		return  $this->model->{$this->relationName}->validate();
	}

	public function save() {
		$foreignKey = $this->relationInfo[WFormRelation::RELATION_FOREIGN_KEY];
		$this->model->{$this->relationName}->$foreignKey = $this->model->primaryKey;
		
		return $this->model->{$this->relationName}->save();
	}


}
