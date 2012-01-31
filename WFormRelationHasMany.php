<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WFormRelationHasMany extends WFormRelation {

	public $type = CActiveRecord::HAS_MANY;

	public function setAttributes($bunchOfAttributes) {
		parent::setAttributes($bunchOfAttributes);

		$relationClass = $this->info[WFormRelation::RELATION_CLASS];
		$relationPk = $relationClass::model()->getMetaData()->tableSchema->primaryKey;
		
		$modelsDictionary = array();
		foreach ($this->getRelatedModels() as $relationModel) {
			if ($relationModel->primaryKey) {
				$modelsDictionary[$relationModel->primaryKey] = $relationModel;
			}
		}

		$relationModels = array();

		foreach ($bunchOfAttributes as $key => $attributes) {
			if (isset($attributes[$relationPk])) {
				if (isset($modelsDictionary[$attributes[$relationPk]])) {
					$relationModel = $modelsDictionary[$attributes[$relationPk]];
				} else {
					$relationModel = $relationClass::model()->findByPk($attributes[$relationPk]) ?: new $relationClass();
				}
			} else {
				$relationModel = new $relationClass();
			}

			$relationModel->attributes = $attributes;
			$relationModels[$key] = $relationModel;
		}

		$this->model->{$this->name} = $relationModels;
	}

	public function validate() {
		$isValid = true;

		$relatedModels = $this->getRelatedModels();
		if (count($relatedModels) == 0 && $this->required)
			return false;

		foreach ($relatedModels as $key => $relationModel) {
			if (!$relationModel->validate()) {
				if ($this->unsetInvalid) {
					unset($relatedModels[$key]);
					$this->model->{$this->name} = $relatedModels;
				} else {
					$isValid = false;
				}
			}
		}
		return $isValid;
	}

	public function save() {
		$foreignKey = $this->info[WFormRelation::RELATION_FOREIGN_KEY];

		if ($this->mode == self::MODE_REPLACE) {
			foreach($this->getActualRelatedModels() as $model)
				$this->addToLazyDelete($model);
		}

		$relatedModels = $this->getRelatedModels();
		if (count($relatedModels) == 0 && $this->required)
			return false;

		$isSuccess = true;
		foreach ($relatedModels as $index => $relationModel) {
			$this->removeFromLazyDelete($relationModel);

			$relationModel->$foreignKey = $this->model->primaryKey;
			$isSuccess = $relationModel->save() && $isSuccess;
		}

		return $isSuccess;
	}

	public function getRelatedModels() {
		if (!$this->model->{$this->name} || (!$this->isAttributesPerformed() && $this->isPreloaded())) {
			$this->model->{$this->name} = array();
		}

		return (array) $this->model->{$this->name};
	}

	public function getActualRelatedModels() {
		if ($this->model->isNewRecord)
			return array();

		$modelClone = clone $this->model;
		return (array) $modelClone->getRelated($this->name, true);
	}

	public function delete() {
		if (!$this->cascadeDelete)
			return true;

		$isSuccess = true;
		foreach($this->getActualRelatedModels() as $model)
			$isSuccess = $model->delete() && $isSuccess;

		return $isSuccess;
	}
}
