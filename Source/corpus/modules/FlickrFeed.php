<?

$_meta['name'] = 'FlickrFeed';
$_meta['callable'] = true;

if( !$shutup ) :

$_config->defaults(
	array(
		'CACHEDIR' => 'cache/',
		'USERNAME' => '30444376@N08',
		'DIVCLASS' => 'FlickrWrap',
		'IMGCLASS' => 'FlickrImg',
		'IMGWIDTH' => 115,
		'IMGCOUNT' => 9,
		'CACHEDAYS' => 3,
	)
);

$username = urlencode( firstNotEmpty( $data[0], $_config->USERNAME ) );
$photoWidth = firstNotEmpty( $data[1], $_config->IMGWIDTH );
$numberToDisplay = (int)firstNotEmpty( $data[2], $_config->IMGCOUNT );

$feedUrl = 'http://api.flickr.com/services/feeds/photos_public.gne?id=' . $username . '&format=json&nojsoncallback=1';
$cacheKey = md5($feedUrl);

if( !$_cache->isExpired( $cacheKey ) ) {
	$feed = $_cache->$cacheKey;
}else{
	if( $feed = file_get_contents( $feedUrl ) ) {
		$_cache->set( $cacheKey, $feed, $_config->CACHEDAYS, Cache::DAY, false );
	}else{
		$feed = $_cache->$cacheKey;
		$_ms->add('Error Communicating with Flickr API, Error 1', true);
	}
}

$flickr = json_decode($feed, true);
echo '<div class="'. $_config->DIVCLASS .'">';
foreach( $flickr['items'] as $flick ) {

	$cacheFName = $_config->CACHEDIR . md5( $flick['media']['m'] ) . '.flickr.jpg';
	if( !file_exists( $cacheFName ) ) {
		copy( $flick['media']['m'], $cacheFName );
	}
	
	echo '<div class="'. $_config->IMGCLASS .'" title="'.htmlE( $flick['title'] ).'">
		<img src="' . htmlE( 'images/displayImage.php?src=../'.$cacheFName.'&w='.(int)$photoWidth ) .'" alt="' . htmlE( $flick['title'] ) . '" /></div>';

	if(++$count >= $numberToDisplay) break 1;
}
echo '</div>';
echo '<br clear="all" />';
endif;