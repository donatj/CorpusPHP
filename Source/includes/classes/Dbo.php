<?

abstract class Dbo {
	
	protected $_id;
	private static $data_cache;
	
//	static $data_cache;
	
	public function __set($name, $value) {
		
		if( $name == 'id' ) {
			$this->_id = $value;
			self::$data_cache[ static::$_table ][ $this->_id ] = null;
		}
	}
	
	public function __get($name) {

		if( $name == 'id') {
			return $this->_id;
		}

		if( !isset( self::$data_cache[ static::$_table ][ $this->_id ] ) ) {
			self::$data_cache[ static::$_table ][ $this->_id ] = null;
		}

		$this->_lazyLoad();

		if( !isset( self::$data_cache[ static::$_table ][ $this->_id ] ) ) {
			die('oh no');
		}
		
		return self::$data_cache[ static::$_table ][ $this->_id ][ $name ];
	}

	public static function __callStatic($name, $args) {

		if(startsWith($name, 'by_')) {

		}else{
			die('Undefined Static Method');
		}

	}
	
	private function _lazyLoad() {
		$load = array();
		foreach( self::$data_cache[ static::$_table ] as $key => $value ) {
			if( $value === null ) {
				$load[] = $key;
			}
		}
		
		if( count($load) > 0 ) {
			$query_str = 'Select * From `'.static::$_table.'` WHERE `'. static::$_pk.'` in ("'.implode( '","', $load ).'")';
			$query = db::query( $query_str );
			while( $load_row = mysql_fetch_array( $query ) ) {
				self::$data_cache[ static::$_table ][ $load_row[static::$_pk] ] = $load_row;
			}
		}
	}
	
}