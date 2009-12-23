<?
/**
* Configuration Loader / Modifier
* 
* @author Jesse G. Donat
* @version 1.1.2
* @package CorpusPHP
* @todo figure out a good global workaround without constants?
* @property-read string ... gets configuration values by key
* @property-write mixed ... sets configuration values by key in the database.  Does not redefine constants
*/
class Configuration {
	
	private $data = array();
	
	function __construct() {
		$qry = db::query("Select * From config", true);
		while($config = mysql_fetch_array($qry)) { 
			$this->data[ $config['key'] ] = $config['value'];
			//backwards compatability.  Remove
			define($config['key'], $config['value']);
		}
	}
	
	public function __get( $key ) {
		return $this->data[ $key ];
	}
	
	public function __set( $key, $value ) {
		$this->data[ $key ] = $value;
		db::perform('config', array('key' => $key, 'value' => $value), true);
	}
	
	public function __isset( $key ) {
		return isset( $this->data[ $key ] );
	}
	
}