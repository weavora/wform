<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WRelatedModel {
	const RELATION_TYPE = 0;
	const RELATION_CLASS = 1;
	const RELATION_FOREIGN_KEY = 2;

	static public function getInstance($model, $relationName, $relationInfo) {
		switch($relationInfo[self::RELATION_TYPE]) {
			case CActiveRecord::HAS_ONE: $relatedModel = new WRelatedModelHasOne(); break;
			case CActiveRecord::HAS_MANY: $relatedModel = new WRelatedModelHasMany(); break;
			case CActiveRecord::BELONGS_TO: $relatedModel = new WRelatedModelBelongsTo(); break;
			case CActiveRecord::MANY_MANY: $relatedModel = new WRelatedModelManyMany(); break;
		}

		$relatedModel->model = $model;
		$relatedModel->relationName = $relationName;
		$relatedModel->relationInfo = $relationInfo;

		return $relatedModel;
	}
}
