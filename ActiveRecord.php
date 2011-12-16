<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class ActiveRecord extends CActiveRecord {

	public function onUnsafeAttribute($name, $value)
	{
		$event = new CEvent($this, array('name' => $name, 'value' => $value));
		$this->raiseEvent('onUnsafeAttribute', $event);
		return parent::onUnsafeAttribute($name, $value);
	}

	public function validate($attributes = null, $clearErrors = true) {
		$parentValidate = parent::validate($attributes, $clearErrors);
		$event = new CModelEvent($this);
		$this->onValidate($event);
		return $parentValidate && $event->isValid;
	}

	public function save($runValidation=true,$attributes=null) {
		$parentSave = parent::save($runValidation,$attributes);
		if ($parentSave) {
		    $event = new CModelEvent($this);
			$this->onSave($event);
			return $parentSave && $event->isValid;
		}
		return $parentSave;
	}

	protected function onValidate($event) {
		$this->raiseEvent('onValidate',$event);
	}

	public function onSave($event) {
		$this->raiseEvent('onSave',$event);
	}
}
