<?
/**
* @name Damned Simple Image Scaler
* @author Jesse G. Donat
* 
* Created: March 25, 2009
* Updated: February 11 2010
* 
*/
error_reporting( E_ALL ^ E_NOTICE );

if( !file_exists($_GET['src']) ) {
	header("HTTP/1.0 404 Not Found");
	header('Content-Type: image/gif');
	echo base64_decode('R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==');
	die();	
}

$orig = getimagesize( $_GET['src'] );

if( $_GET['w'] > 0 && $_GET['h'] > 0 ) {
	if( $_GET['w'] / $_GET['h'] > $orig[0] / $orig[1] ) {
		unset($_GET['w']); //limit by height
	}else{
		unset($_GET['h']); //limit by width
	}
}

if( $_GET['w'] > 0 ) {
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
	if( imageistruecolor( $sourceFile ) ) {
		$newImage = imagecreatetruecolor($w,$h);
	}else{
		$newImage = imagecreate($w,$h);
	}
	
	imagecopyresampled($newImage, $sourceFile, 0, 0, 0, 0, $w, $h, $orig[0], $orig[1]);
	imagejpeg($newImage, $cacheName, 90);
}

$fp = fopen($cacheName, 'rb');
header("Pragma: public");
header("X-Powered-By: CorpusPHP");
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + (60*60*24*120) ) . ' GMT');
header("Last-Modified: ".gmdate( "D, d M Y H:i:s", filemtime($cacheName) )." GMT");
header('Content-type: image/jpeg');
header("Content-Transfer-Encoding:  binary"); 
header("Content-Length: " . filesize($cacheName) ); 
fpassthru($fp);