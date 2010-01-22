<?

$apikey = '';
$cacheDir = 'cache/';

if( !$shutup ) :

if( empty($apikey) ) { $_ms->add('Last.fm API Key Required'); }

$username = firstNotEmpty( $data[0], 'donatj' );
$albumWidth = firstNotEmpty( $data[1], 115 );
$numberToDisplay = firstNotEmpty( $data[2], 5 );

$feedUrl = "http://ws.audioscrobbler.com/1.0/user/$username/weeklyalbumchart.xml";
$cacheKey = md5($feedUrl);

if( Cache::isCached( 'LastFM', $cacheKey ) ) {
	$chartXml = Cache::get( 'LastFM', $cacheKey );
}else{
	$chartXml = file_get_contents($feedUrl);
	Cache::set( 'LastFM', $cacheKey, $chartXml , 3, 'DAY');
}

$dom = DOMDocument::loadXML( $chartXml );

$Albums = $dom->getElementsByTagName( 'album' );
echo '<div class="LastFMwrap">';
foreach($Albums as $Album) {
	
	$artistName = $Album->getElementsByTagName('artist')->item(0)->nodeValue;
	$albumName = $Album->getElementsByTagName('name')->item(0)->nodeValue;
	$mbid = $Album->getElementsByTagName('mbid')->item(0)->nodeValue;
	
	if( $mbid ) { //most reliable
		$albumAddr = "http://ws.audioscrobbler.com/2.0/?method=album.getinfo&api_key=$apikey&mbid=" . $mbid;
		$cacheName = md5( $mbid );
	}else{
		$albumAddr = "http://ws.audioscrobbler.com/2.0/?method=album.getinfo&api_key=$apikey&artist=" . urlencode($artistName) . '&album=' . urlencode($albumName);
		$cacheName = md5( $artistName .'x'. $albumName );
	}
	
	
	
	$cacheFName = $cacheDir . $cacheName . '.lastfm.jpg';
	
	if( !file_exists($cacheFName) ) {
		
		if( Cache::isCached( 'LastFM', $cacheName ) ) {
			$albumXml = Cache::get( 'LastFM', $cacheName );
		}else{
			$albumXml = file_get_contents($albumAddr);
			Cache::set( 'LastFM', $cacheName, $albumXml , 10);
		}
		
		$albumDom = DOMDocument::loadXML($albumXml);
		$albumImages = $albumDom->getElementsByTagName( 'image' );
		//echo $albumAddr;
		
		foreach( $albumImages as $aImg ){ 
			if( $aImg->hasAttribute('size') && $aImg->getAttribute('size') == 'extralarge' && nempty($aImg->nodeValue) ) {
				copy( $aImg->nodeValue, $cacheFName );
				break;
			}
		}
	}
	
	if( file_exists($cacheFName) ) {
		echo '<div class="LastFMimg" title="'.htmlE( $artist['name'] ).'">
			<img src="' . htmlE( 'images/displayImage.php?src=../'.$cacheFName.'&w='.(int)$albumWidth ) .'" alt="' . htmlE( $artistName . ' - ' . $albumName ) . '" /></div>';
		
		if(++$count >= $numberToDisplay) break 1;
		if($count % 3 == 0) echo '<br clear="all" />';
	}
}

echo '</div>';

endif;