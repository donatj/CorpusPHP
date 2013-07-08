<?php

$_meta['name'] = 'FlickrFeed';
$_meta['callable'] = true;

if( !$shutup ) :

$this->_config->defaults(
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

$username = urlencode( firstNotEmpty( $this->data[0], $this->_config->USERNAME ) );
$photoWidth = firstNotEmpty( $this->data[1], $this->_config->IMGWIDTH );
$numberToDisplay = (int)firstNotEmpty( $this->data[2], $this->_config->IMGCOUNT );

$feedUrl = 'http://api.flickr.com/services/feeds/photos_public.gne?id=' . $username . '&format=json&nojsoncallback=1';
$cacheKey = md5($feedUrl);

if( !$this->_cache->isExpired( $cacheKey ) ) {
	$feed = $this->_cache->$cacheKey;
}else{
	if( $feed = file_get_contents( $feedUrl ) ) {
		$this->_cache->set( $cacheKey, $feed, $this->_config->CACHEDAYS, Cache::DAY, false );
	}else{
		$feed = $this->_cache->$cacheKey;
		$_ms->add('Error Communicating with Flickr API, Error 1', true);
	}
}

$flickr = json_decode($feed, true);
echo '<div class="'. $this->_config->DIVCLASS .'">';
foreach( $flickr['items'] as $flick ) {

	$cacheFName = $this->_config->CACHEDIR . md5( $flick['media']['m'] ) . '.flickr.jpg';
	if( !file_exists( $cacheFName ) ) {
		copy( $flick['media']['m'], $cacheFName );
	}
	
	echo '<div class="'. $this->_config->IMGCLASS .'" title="'.htmlE( $flick['title'] ).'">
		<img src="' . htmlE( 'images/displayImage.php?src=../'.$cacheFName.'&w='.(int)$photoWidth ) .'" alt="' . htmlE( $flick['title'] ) . '" /></div>';

	if(++$count >= $numberToDisplay) break 1;
}
echo '</div>';
echo '<br style="clear: both;" />';
endif;