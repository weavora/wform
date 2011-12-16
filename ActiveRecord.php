<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class ActiveRecord extends CActiveRecord {

	public function onUnsafeAttribute($name,$value)
	{
		$event = new CEvent($this, array('name' => $name, 'value' => $value));
		$this->raiseEvent('onUnsafeAttribute',$event);
		return parent::onUnsafeAttribute($name, $value);
	}

	public function validate($attributes = null, $clearErrors = true) {
		$validate = parent::validate($attributes, $clearErrors);
		$event = new CModelEvent($this);
		$this->onValidate($event);
		return $validate && $event->isValid;
	}

	protected function onValidate($event) {
		$this->raiseEvent('onValidate',$event);
	}
}
