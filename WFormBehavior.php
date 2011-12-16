<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WFormBehavior extends CActiveRecordBehavior {

	public $relations = array();
	private $_relationsMap = array();

	public function events() {
		return array_merge(parent::events(), array(
			'onAfterConstruct' => 'afterConstruct',
			'onUnsafeAttribute' => 'unsafeAttribute',
			'onValidate' => 'validate',
		));
	}

	public function afterConstruct($event) {
		$senderRelations = $event->sender->relations();
		foreach ($this->relations as $relation) {

			// populate relationsMap
			$this->_relationsMap[$relation] = array(
				'relation' => $relation,
				'type' => $senderRelations[$relation][0],
				'class' => $senderRelations[$relation][1],
			);

			// init relations with type HAS_ONE
			if ($senderRelations[$relation][0] == CActiveRecord::HAS_ONE) {
			    $event->sender->$relation = new $senderRelations[$relation][1]();
			}
		}
	}

	public function unsafeAttribute($event) {
		if (isset($this->relations[$event->params['name']])) {

		}
	}

	public function validate($event) {
		// iterate sub-models
			// validate sub-model
		$event->isValid = false;
	}
}
