<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WFormRelationBelongsTo extends WFormRelationHasOne {

	public $type = CActiveRecord::BELONGS_TO;

	public function save() {
		if (!$this->model->{$this->name}->save())
			return false;

		$foreignKey = $this->info[WFormRelation::RELATION_FOREIGN_KEY];
		$this->model->{$foreignKey} = $this->model->{$this->name}->primaryKey;
		
		return true;
	}
}
