<?
/**
* Configuration Loader / Modifier
* 
* @author Jesse G. Donat
* @version 2 alpha
* @package CorpusPHP
* @todo figure out a good global workaround without constants?
* @property-read string ... gets configuration values by key
* @property-write mixed ... sets configuration values by key in the database.  Does not redefine constants
*/
class Configuration {
	
	private $data = array();
	private $module;
	
	function __construct($module = false) {
		$this->module = $module;
		$qry = db::query("Select `key`, `value` From config Where module = '".db::input($this->module)."' ", true);
		
		while($config = mysql_fetch_array($qry)) {
			$this->data[ $config['key'] ] = $config['value'];
			if( !$module ) { define($config['key'], $config['value']); }
		}
	}
	
	public function __get( $key ) {
		return $this->data[ $key ];
	}
	
	public function __set( $key, $value ) {
		$this->set( $key, $value );
	}
	
	private function set( $key, $value, $replace = true, $fatal = true, $update = true ) {
		if( $update ) { $this->data[ $key ] = $value; }
		db::perform('config', array( 'module' => $this->module, 'key' => $key, 'value' => $value), $replace, $fatal );
	}
	
	public function __isset( $key ) {
		return isset( $this->data[ $key ] );
	}
	
	public function defaults( $defaults ) {
		$cnt = db::fetch( "select count(*) from config where `key` in ( '" . implode( "','", array_keys( $defaults ) ) . "' ) and module = '".db::input($this->module)."'", db::SCALAR );
		if( $cnt < count( $defaults ) ) {
			$this->data = array_merge( $defaults, $this->data );
			foreach( $defaults as $dk => $dv ) {
				$this->set( $dk, $dv, false, false, false );
			}
		}		
	}
	
}