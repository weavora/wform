<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WRelatedModelHasMany {

	public $type;
	public $relationName;
	public $relationInfo;
	public $model;

	public function __construct() {
		$this->type = CActiveRecord::HAS_MANY;
	}

	public function initRelation() {
		$relationName = $this->relationName;
		$relationClass = $this->relationInfo[WRelatedModel::RELATION_CLASS];
		$this->model->$relationName = array(new $relationClass());
	}

	public function setAttributes($bunchOfAttributes) {
		$relationName = $this->relationName;
		$relationClass = $this->relationInfo[WRelatedModel::RELATION_CLASS];

		$modelsDictionary = array();
		if ($this->model->$relationName) {
			foreach ($this->model->$relationName as $relationModel) {
			    if ($relationModel->id) {
			        $modelsDictionary[$relationModel->id] = $relationModel;
			    }
			}
		}

		$relationModels = array();
		foreach ($bunchOfAttributes as $attributes) {
			if (isset($attributes['id']) && isset($modelsDictionary[$attributes['id']])) {
				$relationModel = $modelsDictionary[$attributes['id']];
			} else {
				$relationModel = new $relationClass();
			}
			$relationModel->attributes = $attributes;
			$relationModels[] = $relationModel;
		}
		$this->model->$relationName = $relationModels;
	}

	public function validate() {
		$relationName = $this->relationName;
		$validate = true;
		$relationModels = $this->model->$relationName;
		if ($this->model->$relationName) {
		    foreach ($relationModels as $index => $relationModel) {
		        $validate = $relationModels[$index]->validate() && $validate;
		    }
		}
		$this->model->$relationName = $relationModels;
		return $validate;
	}

	public function save() {
		$relationName = $this->relationName;
		$success = true;
		$relationModels = $this->model->$relationName;
		$foreignKey = $this->relationInfo[WRelatedModel::RELATION_FOREIGN_KEY];
		if ($this->model->$relationName) {
		    foreach ($relationModels as $index => $relationModel) {
		    	$relationModels[$index]->$foreignKey = $this->model->id;
		        $success = $relationModels[$index]->save() && $success;
		    }
		}
		$this->model->$relationName = $relationModels;
		return $success;
	}

}
