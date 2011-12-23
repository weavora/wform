<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WActiveRecord extends CActiveRecord {

	// @todo any ideas how to prevent using custom event for that ?
	public function onUnsafeAttribute($name, $value)
	{
		$event = new CEvent($this, array('name' => $name, 'value' => $value));
		$this->raiseEvent('onUnsafeAttribute', $event);
		return parent::onUnsafeAttribute($name, $value);
	}
}
