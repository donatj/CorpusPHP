<?

abstract class Dbo {
	
	protected $_table;
	protected $_pk;
	protected $_id;
	
//	static $data_cache;
	
	public function __set($name, $value) {
		global $_dbo_data_cache;
		
		if( $name == 'id' ) {
			$this->_id = $value;
			if( !isset( $_dbo_data_cache[ $this->_table ][ $this->_id ] ) ) {
				$_dbo_data_cache[ $this->_table ][ $this->_id ] = null;
			}
		}
	}
	
	public function __get($name) {
		global $_dbo_data_cache;
		
		$this->_lazyLoad();
		print_r( $data_cache );
		
		return $_dbo_data_cache[ $this->_table ][ $this->_id ][ $name ];
	}
	
	private function _lazyLoad() {
		global $_dbo_data_cache;
		
		$load = array();		
		foreach( $_dbo_data_cache[ $this->_table ] as $key => $value ) {
			if( $value === null ) {
				$load[] = $key;
			}
		}
		
		if( count($load) > 0 ) {
			$query_str = 'Select * From `'.$this->_table.'` WHERE `'.$this->_pk.'` in ('.implode( ',', $load ).')';
			$query = db::query( $query_str );
			while( $load_row = mysql_fetch_array( $query ) ) {
				$_dbo_data_cache[ $this->_table ][ $load_row[$this->_pk] ] = $load_row;
			}
		}
		
	}
	
}