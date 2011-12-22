<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WFormBehavior extends CActiveRecordBehavior {

	/**
	 * @var array what relations we should save
	 */
	public $relations = array();

	protected $relatedModels = array();

	public function events() {
		return array_merge(parent::events(), array(
			// @todo any ideas how to prevent using custom event for that ? Maybe create attributes dynamic for relations?
			'onUnsafeAttribute' => 'unsafeAttribute',
		));
	}

	/**
	 * Initialize related models
	 * @param $event
	 * @return void
	 */
	public function afterConstruct($event) {
		$this->_buildRelatedModel($event->sender);
	}

	/**
	 * Rebuild related models
	 * @param $event
	 * @return void
	 */
	public function afterFind($event) {
		$this->_buildRelatedModel($event->sender);
	}

	/**
	 * Set related models attributes
	 * @param $event
	 * @return void
	 */
	public function unsafeAttribute($event) {
		$relation = $event->params['name'];
		if (isset($this->relatedModels[$relation])) {
			$this->relatedModels[$relation]->setAttributes($event->params['value']);
		}
	}

	/**
	 * Validate related models
	 * @param $event
	 * @return void
	 */
	public function afterValidate($event) {
		$model = $event->sender;
		foreach ($this->relatedModels as $relatedModel) {
			if (!$relatedModel->validate()) {
				$model->addError($relatedModel->relationName, $relatedModel->relationName . ' is not valid');
			}
		}
	}

	/**
	 * Save related models which depends on parent model
	 * @return void
	 */
	public function afterSave($event) {
		foreach ($this->relatedModels as $relatedModel) {
			if (in_array($relatedModel->type, array(CActiveRecord::HAS_ONE, CActiveRecord::HAS_MANY, CActiveRecord::MANY_MANY))) {
				$relatedModel->save();
			}
		}
	}

	/**
	 * Save related models which affect to parent models
	 * @return void
	 */
	public function beforeSave($event) {
		foreach ($this->relatedModels as $relatedModel) {
			if (in_array($relatedModel->type, array(CActiveRecord::BELONGS_TO, CActiveRecord::MANY_MANY))) {
				if (!$relatedModel->save())
					$event->isValid = false;
			}
		}
	}

	/**
	 * Add relations to criteria.with
	 * @param $event
	 * @return void
	 */
	public function beforeFind($event) {
		// @todo implement
	}

	/**
	 * Rebuild related models
	 * 
	 * @param $parentModel
	 * @return void
	 */
	protected function _buildRelatedModel($parentModel) {
		$parentRelations = $parentModel->relations();
		$this->relatedModels = array();
		foreach ($this->relations as $relation) {
			if (array_key_exists($relation, $parentRelations)) {
				$this->relatedModels[$relation] = WRelatedModel::getInstance($parentModel, $relation, $parentRelations[$relation]);
			}
		}
	}
}
