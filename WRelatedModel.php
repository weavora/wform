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
		if ($relationInfo[self::RELATION_TYPE] == CActiveRecord::HAS_ONE) {
			$relatedModel = new WRelatedModelHasOne();
		} else if ($relationInfo[self::RELATION_TYPE] == CActiveRecord::HAS_MANY) {
			$relatedModel = new WRelatedModelHasMany();
		}

		$relatedModel->model = $model;
		$relatedModel->relationName = $relationName;
		$relatedModel->relationInfo = $relationInfo;

		return $relatedModel;
	}
}
