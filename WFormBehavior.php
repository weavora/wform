<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WFormBehavior extends CActiveRecordBehavior {
	public $relations = array();
	protected $relatedModels = array();

	public function events() {
		return array_merge(parent::events(), array(
			'onAfterConstruct' => 'afterConstruct',
			'onUnsafeAttribute' => 'unsafeAttribute',
			'onValidate' => 'validate',
			'onSave' => 'save',
			'onAfterFind' => 'afterFind',
			'onBeforeFind' => 'beforeFind',
		));
	}

	public function afterConstruct($event) {
		$senderRelations = $event->sender->relations();
		foreach ($this->relations as $relation) {
			$this->relatedModels[$relation] = WRelatedModel::create($event->sender, $relation, $senderRelations[$relation]);
			$this->relatedModels[$relation]->initRelation();
		}
	}

	public function unsafeAttribute($event) {
		$relation = $event->params['name'];
		if (isset($this->relatedModels[$relation])) {
			$this->relatedModels[$relation]->setAttributes($event->params['value']);
		}
	}

	public function validate($event) {
		$validate = true;
		foreach ($this->relatedModels as $relatedModel) {
		    $validate = $relatedModel->validate() && $validate;
		}
		$event->isValid = $validate;
	}

	public function save($event) {
		$success = true;
		foreach ($this->relatedModels as $relatedModel) {
			$success = $relatedModel->save() && $success;
		}
		$event->isValid = $success;
	}

	public function afterFind($event) {
		$senderRelations = $event->sender->relations();
		foreach ($this->relations as $relation) {
			$this->relatedModels[$relation] = WRelatedModel::create($event->sender, $relation, $senderRelations[$relation]);
			if (!$event->sender->$relation) {
			    $this->relatedModels[$relation]->initRelation();
			}
		}
	}

	public function beforeFind($event) {
		// var_dump($event->criteria);
	}

}
