<?
/** CorpusPHP Framework **
*
* @package CorpusPHP
* @license http://opensource.org/licenses/mit-license.php
* @version 1.8
* @author Jesse G. Donat
*
* Corpus Motto:
*  It is one thing to insult a man on his character,
* 	but to insult a man on his grade of rice,
* 	that's another thing entirely.
*
* 	June 16, 2009
*
**/

include("includes/app_top.php");

$_content = _::content();

if( $_content === false ) { //if no content (eg: bad page, bad template) sends a 404 header, loads 404 page and such
	$_ms->clear();
	$_content = co::content( '404' );
}

if( !$_meta['raw'] ) { //loads the page wrapped in the main template
	echo co::layout('index', $_content, $_meta['layout'] );
}else{ //loads the page raw for ajax requests and such
	echo $_content;
}