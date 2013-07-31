<?php

class Filter {

	static function process($filter, $content) {
		$filename = $filter . '.php';
		if( file_exists( DWS_APP_FILTER . $filename ) ) {
			$method = require( DWS_APP_FILTER . $filename );
		}elseif( file_exists( DWS_FILTER . $filename ) ) {
			$method = require( DWS_FILTER . $filename );
		}

		if( $method ) {
			return $method( $content );
		}
		
		throw new Exception('Filter "'. $filter .'" not found.', 1);
	}

}