<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class AttachmentForm extends Attachment
{

	public $tempFile = null;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function rules()
	{
		return array_merge(parent::rules(), array(
			array('tempFile', 'safe'),
		));
	}

	public static function create($type)
	{
		$attachmentForm = new AttachmentForm();
		$attachmentForm->object_type = $type;
		return $attachmentForm;
	}

	public function beforeValidate()
	{
		if ($this->file instanceof CUploadedFile) {
			// save to tmp folder
			$tempFile = new WTempFile(Yii::app()->runtimePath);

			if ($this->file->saveAs($tempFile->getPath())) {
				$this->tempFile = $tempFile->getFile();

				// setup proper file_origin
				$this->file_origin = $this->file->getName();
			}
		}
		return true;
	}

	public function saveUploadedFile()
	{
		if (empty($this->file_origin)) {
			if (!$this->isNewRecord)
				$this->delete();
			return false;
		}

		if (empty($this->tempFile)) {
			return false;
		}

		$tempFile = new WTempFile(Yii::app()->runtimePath);
		$tempFile->setFile($this->tempFile);

		if (!$tempFile->isValid()) {
			return false;
		}

		$attachmentDirectory = Yii::app()->runtimePath . '/' . $this->object_type . '/';

		if (!is_dir($attachmentDirectory)) {
			mkdir($attachmentDirectory);
		}

		$fileName = $this->id . '.' . pathinfo($this->file_origin, PATHINFO_EXTENSION);


		if ($tempFile->saveAs($attachmentDirectory . $fileName)) {
			$this->file = $fileName;
			$this->isNewRecord = false;
			$this->tempFile = null;
			$this->save(false);
		}

		$this->tempFile = null;

		return false;
	}

	public function afterSave()
	{
		$this->saveUploadedFile();
		return parent::afterSave();
	}
}
