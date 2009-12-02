<?
/*
* * Damned Simple Image Scaler * *
* By Jesse G. Donat
* March 25, 2009
*/
error_reporting( E_ALL ^ E_NOTICE );
$s_time = microtime(true);

if( !file_exists($_GET['src']) ) {
	header("HTTP/1.0 404 Not Found");
	die();	
}

$orig = getimagesize( $_GET['src'] );

if( $_GET['w'] > 0 && $_GET['h'] > 0 ) {
	$w = (int)$_GET['w'];
	$h = (int)$_GET['h'];
}elseif( $_GET['w'] > 0 ) {
	$w = (int)$_GET['w'];
	$h = ceil( ( $_GET['w'] / $orig[0]) * $orig[1] );
}elseif($_GET['h'] > 0) {
	$w = ceil( ( $_GET['h'] / $orig[1]) * $orig[0] );
	$h = (int)$_GET['h'];
}

$w = min($w, $orig[0]);
$h = min($h, $orig[1]);

$cacheHash = array( $_GET['src'], filemtime( $_GET['src'] ), $w, $h, $orig);
$cacheName = '../cache/' . md5( json_encode($cacheHash) ) . '.thumb.jpg';

if( !file_exists($cacheName) ) {
	$sourceFile = imagecreatefromstring( file_get_contents( $_GET['src'] ) ); //doesn't care about type
	$newImage = imagecreatetruecolor($w,$h);
	imagecopyresampled($newImage, $sourceFile, 0, 0, 0, 0, $w, $h, $orig[0], $orig[1]);
	imagejpeg($newImage, $cacheName, 90);
}

$fp = fopen($cacheName, 'rb');
header("Last-Modified: ".gmdate( "D, d M Y H:i:s", filemtime($cacheName) )." GMT");
header("OrigHeight: " . (int)$orig[0]);
header("OrigWidth: " . (int)$orig[1]);
header('Content-type: image/jpeg');
header('X-Time-Taken: ' . microtime(true) - $s_time);
fpassthru($fp);