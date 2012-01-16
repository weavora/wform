<?php

class WTempFile
{

	private $_tempDirectory = null;
	private $_file = null;

	public function __construct($tempDirectory)
	{
		$this->_tempDirectory = $tempDirectory;
	}

	public function setFile($file)
	{
		$this->_file = $file;
	}

	public function isValid()
	{
		return is_file($this->getPath()) && file_exists($this->getPath());
	}

	public function getPath()
	{
		return $this->_tempDirectory . '/' . $this->getFile();
	}

	public function saveAs($destFile)
	{
		if (!$this->isValid())
			return false;

		return copy($this->getPath(), $destFile);
	}

	public function upload($sourceFile)
	{
		return move_uploaded_file($sourceFile, $this->getPath());
	}

	public function getFile()
	{
		if (empty($this->_file))
			$this->_file = tempnam($this->_tempDirectory, "php");

		return basename($this->_file);
	}
}
