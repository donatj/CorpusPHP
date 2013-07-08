<?php

class Module {

	protected $filename;
	public $data;

	protected $_config;
	protected $_cache;

	public function __construct($name = null, $data = array()) {
		$this->data = $data;
		$this->filename = $name . SUFFIX_PHP;

		$this->_config = new Configuration( $name );
		$this->_cache  = new Cache( $name );
	}

	public function __get($arg) {
		if (isset($this->data[$arg])) {
			return $this->data[$arg];
		}
		return null;
	}

	public function __isset($name) {
		return isset($this->data[$name]);
	}

	public function render() {
		ob_start();
		require(DWS_MODULES.$this->filename);
		$content = ob_get_clean();

		return $content;
	}

	public function filename() {
		return $this->filename;
	}
}