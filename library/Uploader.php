<?php 
class Uploader extends \Upload\File {
	private $file = null;
	private $input = null;
	protected $storage = null;
	private $mimeType = null;
	private $size = null;
	private $directory = null;
	public function __construct (array $uploadInfo = array()) {
		if(!count($uploadInfo) || !isset($uploadInfo['input'])) {
			return ;
		}
		$this->setInput($uploadInfo['input']);
		if(isset($uploadInfo['storage'])) {
			$this->directory = $uploadInfo['storage'];
			$this->setStorage($uploadInfo['storage']);
		} else {
			$this->setStorage();
		}
		$this->setFile();
		if(isset($uploadInfo['storage'])) {
			$this->setName($uploadInfo['name']);
		} else {
			$this->setName();
		}
		$this->setValidation($uploadInfo['validation']);
	}
	public function getInfo() {
		if(is_null($this->file)) {
			return array();
		}
		return array (
			'name'       => $this->file->getNameWithExtension(),
            'extension'  => $this->file->getExtension(),
            'mime'       => $this->file->getMimetype(),
            'size'       => $this->file->getSize(),
            'md5'        => $this->file->getMd5(),
            'dimensions' => $this->file->getDimensions(),
        );
	}
	public function setFile ($input = null,$validations = array(), $storage = null,$name=null) {
		$this->setInput($input);
		$this->setStorage($storage);
		$this->file = new \Upload\File ($this->input, $this->storage);
		$this->setName($name);
		$this->setValidation($validations);
		return $this;
	}
	public function setInput ($input = null) {
		if(is_null($input)) {
			throw new Exception("Input Could Not Be Empty", 1);
			return false;
		}
		$this->input = $input;
		return $this;
	}
	public function setStorage ($storage = null) {
		if(is_null($storage)) {
			$storage = Config::app('uploadStorage');
		}
		$this->directory = $storage;
		$this->storage = new \Upload\Storage\FileSystem($storage);
		return $this;
	}
	public function setName ($name = null) {
		if(is_null($this->file)) {
			throw new Exception("There Is No File Object", 1);
			return false;
		}
		if(is_null($name)) {
			$this->file->setName(uniqid());
		}
	}
	public function setValidation (array $validations = array()) {
		if(is_null($this->file)) {
			throw new Exception("There Is No File Object", 1);
			return false;
		}
		if(!count($validations)) {
			return $this;
		} 
		$array = array();
		foreach ($validations as $key => $value) {
			if(!is_null($key) && !is_null($value)) {
				$class = "\Upload\Validation\\".$key;
				$array[] = new $class($value);
			}
		}
		$this->file->addValidations($array);
		return $this;
	}
	public function getFile () {
		return $this->file;
	}
	public function validate() {
		return $this->file->validate();
	}
	public function getErrors() {
		return $this->file->getErrors();
	}
    public function getPath () {
        return $this->directory.$this->file->getNameWithExtension();
    }
	public function file() {
		return $_FILES[$this->input];
	}
	public function upload() {
		return $this->file->upload();
	}
}