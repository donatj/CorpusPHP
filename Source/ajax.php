<?

include("includes/app_top.php");

$_ajax = co::ajax( $_GET['___ajax_call'], $_GET );

if( $_ajax === false ) { //if no content (eg: bad page, bad template) sends a 404 header, loads 404 page and such
	$_ajax = co::content( '404' );
}

echo $_ajax;