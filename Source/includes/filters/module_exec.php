<?php

return function($content) {

	return preg_replace_callback( PATTERN_MODULE_CALL, function($m){
		global $_ms;
		$modules = co::module_info();
		$module = $modules['calls'][ $m[1] ];
		if( strlen( $module ) > 0 ) {
			$m[3] = str_replace( "'", '"', $m[3] );
			if( $m[2] == '[' ) {
				$data = json_decode( '[' . $m[3] . ']', true );
			}else{
				$data = json_decode( '{' . $m[3] . '}', true );
			}
			return co::module( $module, $data );
		}else{
			$_ms->add( 'Call ' . $m[1] . ' not set', true );
		}

		return false;
	}, $content );
	
};