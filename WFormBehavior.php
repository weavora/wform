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

	/**
	 * @var array scenarios to behavior will be applied
	 */
	public $scenarios = array('*');

	protected $relatedModels = array();

	/**
	 * Extend standard AR behavior events
	 *
	 * @return array
	 */
	public function events() {
		return array_merge(parent::events(), array(
			// @todo any ideas how to prevent using custom event for that ? Maybe create attributes dynamic for relations?
			'onUnsafeAttribute' => 'unsafeAttribute',
		));
	}

	/**
	 * Initialize related models
	 *
	 * @param $event
	 * @return void
	 */
	public function afterConstruct($event) {
		$this->_buildRelatedModel($event->sender);
	}

	/**
	 * Rebuild related models
	 *
	 * @param $event
	 * @return void
	 */
	public function afterFind($event) {
		$this->_buildRelatedModel($event->sender);
	}

	/**
	 * Set related models attributes
	 *
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
	 * Handle file inputs
	 *
	 * @param $event
	 */
	public function beforeValidate($event) {
		$model = $event->sender;

		// create CUploadedFile for all file inputs
		$files = new WFileIterator($model);
		foreach($files as $path => $file) {
			$relation = $this->findRelationByPath($model, $path);
			$attribute = $this->findPathAttribute($path);
			if ($relation) {
				$relation->setAttribute($attribute, $file);
			}
		}
	}

	/**
	 * Validate related models
	 *
	 * @param $event
	 * @return void
	 */
	public function afterValidate($event) {
		$model = $event->sender;
		foreach ($this->relatedModels as $relatedModel) {
			if (!$relatedModel->validate()) {
				$model->addError($relatedModel->name, $relatedModel->name . ' is not valid');
			}
		}
	}

	/**
	 * Save related models which affect to parent models
	 *
	 * @param $event
	 * @return void
	 */
	public function beforeSave($event) {
		foreach ($this->relatedModels as $relatedModel) {
			if (in_array($relatedModel->type, array(CActiveRecord::BELONGS_TO))) {
				if (!$relatedModel->save())
					$event->isValid = false;
			}
		}
	}

	/**
	 * Save related models which depends on parent model
	 *
	 * @param $event
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
	 * Find related model by path (e.g. categories.0.name)
	 *
	 * @param $parentModel parent model
	 * @param $path path
	 * @return CActiveRecord
	 */
	public function findRelationByPath($parentModel, $path) {
		$model = $parentModel;
		$pathPortions = explode(".", trim($path, "."));
		if (count($pathPortions)) {
			$attribute = array_pop($pathPortions);
		}

		foreach($pathPortions as $portion) {
			if (empty($model[$portion]))
				return null;
			$model = $model[$portion];
		}
		return $model;
	}

	/**
	 * Find attribute name into path (e.g. categories.0.name)
	 *
	 * @param $path path
	 * @return string attribute name
	 */
	public function findPathAttribute($path) {
		$pathPortions = explode(".", trim($path, "."));
		return count($pathPortions) ? end($pathPortions) : null;
	}

	/**
	 * Rebuild related models
	 *
	 * @param $parentModel
	 * @return void
	 */
	protected function _buildRelatedModel($parentModel) {
		$this->relatedModels = array();
		foreach ($this->relations as $relation => $options) {
			if (is_numeric($relation)) {
				$relation = $options;
				$options = array();
			}

			if (($relationModel = WFormRelation::getInstance($parentModel, $relation, $options)) !== null)
				$this->relatedModels[$relation] = $relationModel;

		}
	}
}
