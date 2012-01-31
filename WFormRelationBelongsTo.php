<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WFormRelationBelongsTo extends WFormRelationHasOne {

	public $type = CActiveRecord::BELONGS_TO;

	public $required = true;

	public function save() {
		$relationModel = $this->getRelatedModel($this->required);

		if (is_null($relationModel) && !$this->required)
			return true;

		if (!$relationModel->save())
			return false;

		$foreignKey = $this->info[WFormRelation::RELATION_FOREIGN_KEY];
		$this->model->{$foreignKey} = $relationModel->primaryKey;
		
		return true;
	}

	public function getRelatedModel($createNewIfEmpty = true) {
		$relationClass = $this->info[WFormRelation::RELATION_CLASS];

		if (!$this->model->{$this->name} || (!$this->isAttributesPerformed() && $this->isPreloaded())) {
			if (!$createNewIfEmpty)
				return null;

			$this->model->{$this->name} = new $relationClass();
		}

		return $this->model->{$this->name};
	}

	public function delete() {
		return true;
	}
}
