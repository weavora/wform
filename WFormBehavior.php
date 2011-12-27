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

	public function beforeValidate($event) {
		$model = $event->sender;
		$this->_processFilesRecursive($model);
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
				$this->relatedModels[$relation] = WFormRelation::getInstance($parentModel, $relation, $parentRelations[$relation]);
			}
		}
	}

	protected function _processFilesRecursive($parentModel, $path = "", $files = null) {
		if (is_null($files) && isset($_FILES[get_class($parentModel)])) {
			$files = $this->_normalizeFilesRequest($_FILES[get_class($parentModel)]);
		}

		if (empty($files) || !is_array($files))
			return true;

		foreach($files as $key => $subFiles) {
			if ($this->_isFile($subFiles)) {
				if (($model = $this->_getModelByPath($parentModel, $path)) !== null) {
					$model->setAttribute($key, new CUploadedFile($subFiles['name'], $subFiles['tmp_name'], $subFiles['type'], $subFiles['size'], $subFiles['error']));
				}
			} else {
				$this->_processFilesRecursive($parentModel, $path . "." . $key, $subFiles);
			}
		}

		return true;
	}

	protected function _getModelByPath($parentModel, $path) {
		$model = $parentModel;
		$pathPortions = explode(".", trim($path, "."));
		foreach($pathPortions as $portion) {
			if (empty($model[$portion]))
				return null;

			$model = $model[$portion];
		}
		return $model;
	}

	protected function _normalizeFilesRequest($files) {
		$normalizedFiles =  array();
		foreach($files as $key => $value) {
			if (is_array($value)) {
				foreach($value as $k=>$v) {
					$normalizedFiles[$k][$key] = $v;
				}
			} else {
				$normalizedFiles[$key] = $value;
			}
		}

		foreach($normalizedFiles as $k => $v) {
			if (is_array($v))
				$normalizedFiles[$k] = $this->_normalizeFilesRequest($v);
		}

		return $normalizedFiles;
	}

	protected function _isFile($data) {
		return !empty($data['name']) && !empty($data['tmp_name']) && !empty($data['size']) && isset($data['error']);
	}
}
