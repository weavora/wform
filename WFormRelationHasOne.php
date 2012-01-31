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
		parent::setAttributes($attributes);

		if (!is_null($attributes)) {
			$relationModel = $this->getRelatedModel();
			$relationModel->attributes = $attributes;
		} else {
			$this->model->{$this->name} = null;
		}
	}

	public function validate() {
		$relationModel = $this->getRelatedModel($this->required);

		if (is_null($relationModel) && !$this->required)
			return true;


		return  $relationModel->validate();
	}

	public function save() {
		$relationModel = $this->getRelatedModel($this->required);

		if ($this->mode == self::MODE_REPLACE && ($actualModel = $this->getActualRelatedModel()) !== null) {
			$this->addToLazyDelete($actualModel);
		}

		if (is_null($relationModel) && !$this->required)
			return true;

		$foreignKey = $this->info[WFormRelation::RELATION_FOREIGN_KEY];
		$relationModel->$foreignKey = $this->model->primaryKey;

		$this->removeFromLazyDelete($relationModel);
		
		return $relationModel->save();
	}

	public function getRelatedModel($createNewIfEmpty = true) {
		$relationClass = $this->info[WFormRelation::RELATION_CLASS];

		if (!$this->model->{$this->name} || (!$this->isAttributesPerformed() && $this->isPreloaded())) {
			$this->model->{$this->name} = $createNewIfEmpty ? new $relationClass() : null;
		}

		return $this->model->{$this->name};
	}

	public function getActualRelatedModel() {
		if ($this->model->isNewRecord)
			return null;

		$modelClone = clone $this->model;
		return $modelClone->getRelated($this->name, true);
	}

	public function delete() {
		if (!$this->cascadeDelete)
			return true;

		$model = $this->getActualRelatedModel();
		if ($model)
			return $model->delete();

		return true;
	}
}
