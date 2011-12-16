<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WFormBehavior extends CActiveRecordBehavior {

	public $relations = array();
	public $subForms = array();
	private $_relationsMap = array();

	public function events() {
		return array_merge(parent::events(), array(
			'onAfterConstruct' => 'afterConstruct',
			'onUnsafeAttribute' => 'unsafeAttribute',
			'onValidate' => 'validate',
			'onSave' => 'save',
			'onAfterFind' => 'afterFind',
			'onBeforeFind' => 'beforeBefore',
		));
	}

	public function afterConstruct($event) {
		$senderRelations = $event->sender->relations();
		foreach ($this->relations as $relation) {
//			$this->subForms[$relation] = new WSubForm($senderRelations[$relation]);
			// populate relationsMap
			$this->_relationsMap[$relation] = array(
				'relation' => $relation,
				'type' => $senderRelations[$relation][0],
				'class' => $senderRelations[$relation][1],
				'raw' => $senderRelations[$relation],
			);

			// init relations with type HAS_ONE
			if ($this->_relationsMap[$relation]['type'] == CActiveRecord::HAS_ONE) {
			    $event->sender->$relation = $this->_createRelated($relation);
			}
		}
	}

	private function _createRelated($relation) {
		return new $this->_relationsMap[$relation]['class']();
	}

	private function _findByPkRelated($relation) {
//		return new $this->_relationsMap[$relation]['class']();
	}

	public function unsafeAttribute($event) {
		$relation = $event->params['name'];
		if (isset($this->_relationsMap[$relation])) {
			$event->sender->$relation->attributes = $event->params['value'];
		}
	}

	public function validate($event) {
		$validate = true;
		foreach ($this->relations as $relation) {
		    $validate = $event->sender->$relation->validate() && $validate;
		}
		$event->isValid = $validate;
	}

	public function save($event) {
		$success = true;
		foreach ($this->relations as $relation) {
			if ($this->_relationsMap[$relation]['type'] == CActiveRecord::HAS_ONE) {
				$foreignKey = $this->_relationsMap[$relation]['raw'][2];
				$event->sender->$relation->$foreignKey = $event->sender->id;
			    $success = $event->sender->$relation->save() && $success;
			}
		}
		$event->isValid = $success;
	}

	public function onAfterFind($event) {
	}

	public function onBeforeFind($event) {
		// var_dump($event->criteria);
	}
}
