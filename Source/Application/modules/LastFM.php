<?php

$_meta['name'] = 'LastFM';

if( !$shutup ) :

$this->_config->defaults(
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

if( empty( $this->_config->APIKEY ) ) { $_ms->add('Last.fm API Key Required', true); }

$username = firstNotEmpty( $this->data[0], $this->_config->USERNAME );
$albumWidth = firstNotEmpty( $this->data[1], $this->_config->IMGWIDTH );
$numberToDisplay = firstNotEmpty( $this->data[2], $this->_config->IMGCOUNT );

$feedUrl = "http://ws.audioscrobbler.com/2.0/user/{$username}/weeklyalbumchart.xml";
$cacheKey = md5($feedUrl);

if( $this->_cache->isCached( $cacheKey ) ) {
	$chartXml = $this->_cache->$cacheKey;
}else{
	$chartXml = file_get_contents($feedUrl);
	$this->_cache->set( $cacheKey, $chartXml , $this->_config->CACHEDAYS, 'DAY');
}

$dom = DOMDocument::loadXML( $chartXml );

$Albums = $dom->getElementsByTagName( 'album' );
echo '<div class="'. $this->_config->DIVCLASS .'">';
foreach($Albums as $Album) {
	
	$artistName = $Album->getElementsByTagName('artist')->item(0)->nodeValue;
	$albumName = $Album->getElementsByTagName('name')->item(0)->nodeValue;
	$mbid = $Album->getElementsByTagName('mbid')->item(0)->nodeValue;
	
	if( $mbid ) { //most reliable
		$albumAddr = "http://ws.audioscrobbler.com/2.0/?method=album.getinfo&api_key={$this->_config->APIKEY}&mbid=" . $mbid;
		$cacheName = md5( $mbid );
	}else{
		$albumAddr = "http://ws.audioscrobbler.com/2.0/?method=album.getinfo&api_key={$this->_config->APIKEY}&artist=" . urlencode($artistName) . '&album=' . urlencode($albumName);
		$cacheName = md5( $artistName .'x'. $albumName );
	}	
	
	$cacheFName = $this->_config->CACHEDIR . $cacheName . '.lastfm.jpg';
	
	if( !file_exists($cacheFName) ) {
		
		if( $this->_cache->isCached( $cacheName ) ) {
			$albumXml = $this->_cache->$cacheName;
		}else{
			$albumXml = file_get_contents($albumAddr);
			$this->_cache->set( $cacheName, $albumXml , 10 );
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
		echo '<div class="'. $this->_config->IMGCLASS .'" title="'.htmlE( $artist['name'] ).'">
			<img src="' . htmlE( 'images/displayImage.php?src=../'.$cacheFName.'&w='.(int)$albumWidth ) .'" alt="' . $title . '" title="' . $title . '" /></div>';
		
		if(++$count >= $numberToDisplay) break 1;
	}
}

echo '</div>';

endif;