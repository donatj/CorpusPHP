<?php

/**
* Cache Control Class
*
* @package CorpusPHP
* @subpackage Data
* @author Jesse G. Donat
* @version 2 alpha
* @todo Add file caching capabilities
*/
class Cache {
	
	const SECOND = 'SECOND';
	const MINUTE = 'MINUTE';
	const HOUR = 'HOUR';
	const DAY = 'DAY';
	const WEEK = 'WEEK';
	const MONTH = 'MONTH';
	const QUARTER = 'QUARTER';
	const YEAR = 'YEAR';
	
	private $data = array();
	private $module;
	
	function __construct($module = false) {
		self::cleanup();
		$this->module = $module;
		$qry = db::query("Select `expires`, `autoclear` From cache Where module = '".db::input($this->module)."' ", true);
		
		while($cache = mysql_fetch_array($qry)) {
			$this->data[ $config['key'] ] = $cache; //$config['value'];
		}
	}
	
	public function __get( $key ) {
		return self::get( $key );
	}
	
	public function __set( $key, $value ) {
		die('Overloaded Set-ing Not Currently Supported');
		//$this->data[ $key ] = $value;
		//db::perform('cache', array('module' => $this->module, 'key' => $key, 'value' => $value), true);
	}
	
	public function __isset( $key ) {
		return self::isCached( $key );
	}

	public function get($key) {
		return unserialize( db::fetch("select value from cache where module = '" .db::input( $this->module ). "' and `key` = '".db::input($key)."'", db::SCALAR) );
	}
	
	public function isCached($key) {
		return db::fetch("select count(*) from cache where module = '" .db::input( $this->module ). "' and `key` = '".db::input($key)."'", db::SCALAR) > 0;
	}
	
	function isExpired($key) {
		$cache = db::fetch("select now() > expires from cache where module = '" .db::input( $this->module ). "' and `key` = '".db::input($key)."'", db::SCALAR);
		return $cache || is_null($cache);
	}
	
	static private function cleanup(){
		db::query("Delete from cache where autoclear and now() > expires");
	}
	
	public function set($key, $value, $expires = 30, $interval = self::MINUTE, $autoclear = true) {
		self::cleanup();
		db::perform('cache', array(
			'module' => $this->module, 
			'key' => $key, 
			'value' => serialize( $value ), 
			'expires' => array(true, 'date_add(now(), INTERVAL '.abs($expires).' ' .$interval. ')'),
			'autoclear' => (int)$autoclear, 
		), true);
	}
	
}