<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WFileIterator extends CMap {

	public function __construct($model) {
		$modelClass = get_class($model);
		$files = $this->getFiles($modelClass);
		$this->copyFrom($files);
	}

	public function getFiles($key) {
		if (!isset($_FILES[$key])) {
			return array();
		}

		$files = $this->_normalize($_FILES[$key]);

		return $this->_toPaths($files);
	}

	public function _toPaths($files) {
		$complete = false;
		while(!$complete) {
			$complete = true;
			foreach($files as $key => $file) {
				if (!($file instanceof CUploadedFile)) {
					if (!$this->_isFile($file)) {
						$complete = false;
						if (is_array($file)) {
							foreach($file as $subKey => $subFile) {
								$files[$key . '.' . $subKey] = $subFile;
							}
						}
						unset($files[$key]);
					} elseif ($file['error'] != UPLOAD_ERR_OK) {
						unset($files[$key]);
					} else {
						$files[$key] = new CUploadedFile($file['name'], $file['tmp_name'], $file['type'], $file['size'], $file['error']);
					}
				}
			}
		}
		return $files;
	}

	protected function _normalize($files = array()) {
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
				$normalizedFiles[$k] = $this->_normalize($v);
		}

		return $normalizedFiles;
	}

	protected function _isFile($data) {
		return isset($data['name']) && isset($data['tmp_name']) && isset($data['size']) && isset($data['error']);
	}
}
