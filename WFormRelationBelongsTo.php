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

	public function delete() {
		return true;
	}
}
