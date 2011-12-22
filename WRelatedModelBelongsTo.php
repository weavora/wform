<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WRelatedModelBelongsTo extends WRelatedModelHasOne {

	public $type = CActiveRecord::BELONGS_TO;

	public function save() {
		if (!$this->model->{$this->relationName}->save())
			return false;

		$foreignKey = $this->relationInfo[WRelatedModel::RELATION_FOREIGN_KEY];
		$this->model->{$foreignKey} = $this->model->{$this->relationName}->primaryKey;
		
		return true;
	}


}
