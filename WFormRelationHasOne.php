<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WFormRelationHasOne extends WFormRelation {

	public $type = CActiveRecord::HAS_ONE;

	public function setAttributes($attributes) {
		$relationClass = $this->info[WFormRelation::RELATION_CLASS];
		
		if (!$this->model->{$this->name})
			$this->model->{$this->name} = new $relationClass();
		$this->model->{$this->name}->attributes = $attributes;
	}

	public function validate() {
		$relationClass = $this->info[WFormRelation::RELATION_CLASS];

		// @todo what if we define relation into behavior but it absent into form ?
		if (!$this->model->{$this->name})
			$this->model->{$this->name} = new $relationClass();
		return  $this->model->{$this->name}->validate();
	}

	public function save() {
		$foreignKey = $this->info[WFormRelation::RELATION_FOREIGN_KEY];
		$this->model->{$this->name}->$foreignKey = $this->model->primaryKey;
		
		return $this->model->{$this->name}->save();
	}


}
