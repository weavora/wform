<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

// @todo TBD: I think it should be base class for related model types
class WFormRelation {

	const RELATION_TYPE = 0;
	const RELATION_CLASS = 1;
	const RELATION_FOREIGN_KEY = 2;

	public static function getInstance($model, $relationName, $relationInfo) {
		switch($relationInfo[self::RELATION_TYPE]) {
			case CActiveRecord::HAS_ONE: $relatedModel = new WFormRelationHasOne(); break;
			case CActiveRecord::HAS_MANY: $relatedModel = new WFormRelationHasMany(); break;
			case CActiveRecord::BELONGS_TO: $relatedModel = new WFormRelationBelongsTo(); break;
			case CActiveRecord::MANY_MANY: $relatedModel = new WFormRelationManyMany(); break;
		}

		$relatedModel->model = $model;
		$relatedModel->relationName = $relationName;
		$relatedModel->relationInfo = $relationInfo;

		return $relatedModel;
	}

	
}
