<?php

spl_autoload_register(function ( $class ) {
	$class_bits = explode("\\", $class);
	$class = array_pop($class_bits);
	global $__autoload_paths;
	foreach( $__autoload_paths as $path ) {
		if( file_exists($path . $class . '.php') ) {
			include($path . $class  . '.php');
			return true;
		}
	}
});

require("vendor/autoload.php");

session_name(md5(DWS_BASE));
session_start();

header( 'Content-Type: text/html; charset=UTF-8' );
header( 'X-UA-Compatible: IE=edge' );
header( 'X-Powered-By: CorpusPHP 2.x' );

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