<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WFormRelationHasMany extends WFormRelation {

	public $type = CActiveRecord::HAS_MANY;

	public function setAttributes($bunchOfAttributes) {
		$relationClass = $this->info[WFormRelation::RELATION_CLASS];
		$relationPk = $relationClass::model()->getMetaData()->tableSchema->primaryKey;
		
		$modelsDictionary = array();
		foreach ($this->getRelatedModels() as $relationModel) {
			if ($relationModel->primaryKey) {
				$modelsDictionary[$relationModel->primaryKey] = $relationModel;
			}
		}

		$relationModels = array();
		foreach ($bunchOfAttributes as $attributes) {
			if (isset($attributes[$relationPk]) && isset($modelsDictionary[$attributes[$relationPk]])) {
				$relationModel = $modelsDictionary[$attributes[$relationPk]];
			} else {
				$relationModel = new $relationClass();
			}
			$relationModel->attributes = $attributes;
			$relationModels[] = $relationModel;
		}
		$this->model->{$this->name} = $relationModels;
	}

	public function validate() {
		$isValid = true;
		foreach ($this->getRelatedModels() as $relationModel) {
			$isValid = $relationModel->validate() && $isValid;
		}
		return $isValid;
	}

	public function save() {
		$foreignKey = $this->info[WFormRelation::RELATION_FOREIGN_KEY];

		$isSuccess = true;
		foreach ($this->getRelatedModels() as $index => $relationModel) {
			$relationModel->$foreignKey = $this->model->primaryKey;
			$isSuccess = $relationModel->save() && $isSuccess;
		}
		return $isSuccess;
	}

	public function getRelatedModels() {
		if (!$this->model->{$this->name})
			return array();

		return (array) $this->model->{$this->name};
	}
}
