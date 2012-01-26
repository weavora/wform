<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WFormRelationHasOne extends WFormRelation {

	public $type = CActiveRecord::HAS_ONE;

	public $required = true;

	public function setAttributes($attributes) {
		if (!is_null($attributes)) {
			$relationModel = $this->getRelatedModel();
			$relationModel->attributes = $attributes;
		}

//		var_dump($attributes);
	}

	public function validate() {
		$relationModel = $this->getRelatedModel($this->required);

		if (is_null($relationModel) && !$this->required)
			return true;


		return  $relationModel->validate();
	}

	public function save() {
		$relationModel = $this->getRelatedModel($this->required);

		if (is_null($relationModel) && !$this->required)
			return true;

		$foreignKey = $this->info[WFormRelation::RELATION_FOREIGN_KEY];
		$relationModel->$foreignKey = $this->model->primaryKey;
		
		return $relationModel->save();
	}

	public function getRelatedModel($createNewIfEmpty = true) {
		$relationClass = $this->info[WFormRelation::RELATION_CLASS];

		if (!$this->model->{$this->name}) {
			if (!$createNewIfEmpty)
				return null;

			$this->model->{$this->name} = new $relationClass();
		}

		return $this->model->{$this->name};
	}
}
