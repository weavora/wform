<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WRelatedModelManyMany extends WRelatedModelHasMany {

	public $type = CActiveRecord::HAS_MANY;

	public function save() {
		// @todo save many2many relation. Note that this relation depends on both: parent model and related model on same time, so this same would be called twice
		return true;
	}
}
