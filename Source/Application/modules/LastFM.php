<?php

$_meta['name'] = 'LastFM';

if( !$shutup ) :

$_config->defaults(
	array(
		'APIKEY' => '',
		'CACHEDIR' => 'cache/',
		'USERNAME' => 'donatj',
		'DIVCLASS' => 'LastFMwrap',
		'IMGCLASS' => 'LastFMimg',
		'IMGWIDTH' => 115,
		'IMGCOUNT' => 9,
		'CACHEDAYS' => 3,
	)
);

if( empty( $_config->APIKEY ) ) { $_ms->add('Last.fm API Key Required', true); }

$username = firstNotEmpty( $data[0], $_config->USERNAME );
$albumWidth = firstNotEmpty( $data[1], $_config->IMGWIDTH );
$numberToDisplay = firstNotEmpty( $data[2], $_config->IMGCOUNT );

$feedUrl = "http://ws.audioscrobbler.com/2.0/user/{$username}/weeklyalbumchart.xml";
$cacheKey = md5($feedUrl);

if( $_cache->isCached( $cacheKey ) ) {
	$chartXml = $_cache->$cacheKey;
}else{
	$chartXml = file_get_contents($feedUrl);
	$_cache->set( $cacheKey, $chartXml , $_config->CACHEDAYS, 'DAY');
}

$dom = DOMDocument::loadXML( $chartXml );

$Albums = $dom->getElementsByTagName( 'album' );
echo '<div class="'. $_config->DIVCLASS .'">';
foreach($Albums as $Album) {
	
	$artistName = $Album->getElementsByTagName('artist')->item(0)->nodeValue;
	$albumName = $Album->getElementsByTagName('name')->item(0)->nodeValue;
	$mbid = $Album->getElementsByTagName('mbid')->item(0)->nodeValue;
	
	if( $mbid ) { //most reliable
		$albumAddr = "http://ws.audioscrobbler.com/2.0/?method=album.getinfo&api_key={$_config->APIKEY}&mbid=" . $mbid;
		$cacheName = md5( $mbid );
	}else{
		$albumAddr = "http://ws.audioscrobbler.com/2.0/?method=album.getinfo&api_key={$_config->APIKEY}&artist=" . urlencode($artistName) . '&album=' . urlencode($albumName);
		$cacheName = md5( $artistName .'x'. $albumName );
	}	
	
	$cacheFName = $_config->CACHEDIR . $cacheName . '.lastfm.jpg';
	
	if( !file_exists($cacheFName) ) {
		
		if( $_cache->isCached( $cacheName ) ) {
			$albumXml = $_cache->$cacheName;
		}else{
			$albumXml = file_get_contents($albumAddr);
			$_cache->set( $cacheName, $albumXml , 10 );
		}
		
		$albumDom = DOMDocument::loadXML($albumXml);
		$albumImages = $albumDom->getElementsByTagName( 'image' );
		//echo $albumAddr;
		
		foreach( $albumImages as $aImg ){ 
			if( $aImg->hasAttribute('size') && $aImg->getAttribute('size') == 'extralarge' && nempty($aImg->nodeValue) ) {
				if( !@copy( $aImg->nodeValue, $cacheFName ) ) {
				}
				break;
			}
		}
	}
	
	if( file_exists($cacheFName) ) {
		$title = htmlE( $artistName . ' - ' . $albumName );
		echo '<div class="'. $_config->IMGCLASS .'" title="'.htmlE( $artist['name'] ).'">
			<img src="' . htmlE( 'images/displayImage.php?src=../'.$cacheFName.'&w='.(int)$albumWidth ) .'" alt="' . $title . '" title="' . $title . '" /></div>';
		
		if(++$count >= $numberToDisplay) break 1;
	}
}

echo '</div>';

endif;