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
	public $relations = null;

	/**
	 * @var array scenarios to behavior will be applied
	 */
	public $scenarios = array('*');

	protected $relatedModels = array();

	protected $deleteQuery = array();

	// relation was specified into CActiveRecord::with();
	// finder and populated record are different AR instances that's why it should be static :(
	// could potentially cause issue with multi threading
	protected static $preloadedRelations = array();

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
	 * Cache preloaded relation by CActiveRecord::with() method
	 *
	 * @param $event
	 * @return void
	 */
	public function beforeFind($event) {
		$model = $event->sender;
		self::$preloadedRelations = array();
		foreach((array) $model->getDbCriteria()->with as $key => $value) {
			self::$preloadedRelations[] = is_numeric($key) ? $value : $key;
		}
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

				$this->deleteQuery[] = $relatedModel;
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

				$this->deleteQuery[] = $relatedModel;
			}
		}

		foreach($this->deleteQuery as $relation) {
			$relation->lazyDelete();
		}
	}

	/**
	 * Delete related models which depends on parent model
	 *
	 * @param $event
	 * @return void
	 */
	public function afterDelete($event) {
		foreach ($this->relatedModels as $relatedModel) {
			if (in_array($relatedModel->type, array(CActiveRecord::HAS_ONE, CActiveRecord::HAS_MANY, CActiveRecord::MANY_MANY))) {
				$relatedModel->delete();
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
		if (is_null($this->relations)) {
			$this->relations = array_keys($parentModel->relations());
		}
		foreach ($this->relations as $index => $options) {
			$relation = $index;

			if (is_numeric($index)) {
				$relation = $options;
				$options = array();
			}

			if (($relationModel = WFormRelation::getInstance($parentModel, $relation, $options)) !== null) {
				$this->relatedModels[$relation] = $relationModel;
				$this->relatedModels[$relation]->setPreloaded(in_array($relation, self::$preloadedRelations));
			} else {
				unset($this->relations[$index]);
			}

		}
	}
}
