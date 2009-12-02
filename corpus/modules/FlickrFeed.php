<?

$_meta['call'] = 'FlickrFeed';
$cacheDir = 'cache/';

if( !$shutup ) :

$username = urlencode( firstNotEmpty( $data[0], '30444376@N08' ) );
$albumWidth = firstNotEmpty( $data[1], 115 );
$numberToDisplay = (int)firstNotEmpty( $data[2], 10 );


$feedUrl = 'http://api.flickr.com/services/feeds/photos_public.gne?id=' . $username . '&format=php_serial';
$cacheKey = md5($feedUrl);

if( !Cache::isExpired( 'FlickrFeed', $cacheKey ) ) {
	$feed = Cache::get( 'FlickrFeed', $cacheKey );
}else{
	if( $feed = file_get_contents( $feedUrl ) ) {
		Cache::set( 'FlickrFeed', $cacheKey, $feed, 3, 'DAY', false );
	}else{
		$feed = Cache::get( 'FlickrFeed', $cacheKey );
		$_ms->add('Error Communicating with Flickr API, Error 1', true);
	}
}

$flickr = unserialize($feed);
echo '<div class="FlickrWrap">';
foreach( $flickr['items'] as $flick ) {
	$cacheFName = $cacheDir . md5( $flick['l_url'] ) . '.flickr.jpg';
	if( !file_exists( $cacheFName ) ) {
		copy( $flick['l_url'], $cacheFName );
	}
	
	echo '<div class="FlickrImg" title="'.htmlE( $flick['title'] ).'">
		<img src="' . htmlE( 'images/displayImage.php?src=../'.$cacheFName.'&w='.(int)$albumWidth ) .'" alt="' . htmlE( $flick['title'] ) . '" /></div>';

	if(++$count >= $numberToDisplay) break 1;
}
echo '</div>';
echo '<br clear="all" />';
endif;