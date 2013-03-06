<?php

class Module {

	protected $filename;
	public $data;

	public function __construct($filename = null, $data = array()) {
		foreach( $data as $key => $value ) { $this->$key = $value; }
		$this->data = $data;
		$this->filename = $filename;
	}

	public function render() {
		include (DIR_WIDGETS.$this->filename);
	}

	public function filename() {
		return $this->filename;
	}
}