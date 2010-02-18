<?

/*

TODO: auto dbo loader needs a way to prioritize the order things are loaded

function __dbo_loader() {
	$root_classes = get_declared_classes();
	$dbofs = scandir( DWS_DBO );
	foreach( $dbofs as $dbof ) {
		if( is_file( DWS_DBO . $dbof ) ) {
			echo DWS_DBO . $dbof;
			include( DWS_DBO . $dbof );
		}
	}
	print_r($root_classes);
}
__dbo_loader();
*/

session_name(md5(DWS_BASE));
session_start();

header( 'Content-Type: text/html; charset=UTF-8' );
header( 'X-UA-Compatible: IE=edge' );
header( 'X-Powered-By: CorpusPHP 1.x' );

if(function_exists('mb_internal_encoding')) {
	mb_internal_encoding( 'UTF-8' );
}

if( get_magic_quotes_gpc() ) {
	/**
	* Recursively undoes the evil that is magic quotes
	* 
	* @param string|array $var either the value to be cleaned up or an array to recurse into
	* @return array cleaned up value
	*/
	function magic_quotes_fix($var) {
		$tmp = array();
		if(is_array($var)) {
			foreach($var as $key => $value) {
				if(is_array($var[$key])) {
					$tmp[$key] = magic_quotes_fix($value);
				} else {
					$tmp[$key] = stripslashes($value);
				}
			}
		}
		return $tmp;
	}
	$_GET = magic_quotes_fix($_GET);
	$_POST = magic_quotes_fix($_POST);
	$_REQUEST = magic_quotes_fix($_REQUEST);
}