<?

class AssetStack{
	
	static $assets = array();

	static function add( $filename, $type = NULL ) {
		global $_ms;
		if( file_exists( $filename ) ) {
			self::$assets[] = array( 'contents' => file_get_contents( $filename ) );
		}else{
			$_ms->add('Asset ' . $filename . ' not found', true);	
		}
	}

	static function addBlock( $string ) {
	
	}

	static function clear() {
	
	}
	
	static function draw() {
		$json = json_encode( self::$assets );
		echo '<link rel="stylesheet" href="css/'.md5( $json ).'" type="text/css" media="screen,print" />';
	}
	
}