<?

$_meta['name'] = 'TwitterFeed';
$_meta['callable'] = true;

if( !$shutup ) :

$_config->defaults(
	array(
		'USERNAME' => 'donatj',
		'TWEETCOUNT' => 10,
		'DIVCLASS' => 'TwitterFeedWrap',
		'LINKCLASS' => 'TwitterLink',
		'USERCLASS' => 'TwitterUser',
		'CACHEMINUTES' => 15,
		'DATEFORMAT' => 'F j, Y, g:i a',
	)
);

$username = urlencode( firstNotEmpty( $data[0], $_config->USERNAME ) );
$count = (int)firstNotEmpty( $data[1], $_config->TWEETCOUNT );


$feedUrl = 'http://api.twitter.com/1/statuses/user_timeline.json?screen_name=' . $username . '&count=' . $count * 2;
$cacheKey = md5($feedUrl);

if( !$_cache->isExpired( $cacheKey ) ) {
	$feed = $_cache->$cacheKey;
}else{
	if( $feed = file_get_contents( $feedUrl ) ) {
		$_cache->set( $cacheKey, $feed, $_config->CACHEMINUTES, Cache::MINUTE, false );
	}else{
		$feed = $_cache->$cacheKey;
		$_ms->add('Error Communicating with Twitter API, Error 1', true);
	}
}

$twitter = json_decode( $feed, true );
if( is_array( $twitter ) ) {
	echo '<div class="'. $_config->DIVCLASS .'">';
	foreach( $twitter as $tweet ) {
		$tweet['text'] = preg_replace('/([A-Za-z][A-Za-z0-9+.-]{1,120}:[A-Za-z0-9\/](([A-Za-z0-9$_.+!*,;\/?:@&~=-])|%[A-Fa-f0-9]{2}){1,333}(#([a-zA-Z0-9][a-zA-Z0-9$_.+!*,;\/?:@&~=%-]{0,1000}))?)/', '<a href="$1" target="_blank" class="'. $_config->LINKCLASS .'">$1</a>', $tweet['text']);
		$tweet['text'] = preg_replace('/@([A-Za-z0-9_]+)/', '<a href="http://twitter.com/$1" target="_blank" class="'. $_config->LINKCLASS .' '. $_config->USERCLASS .'">@$1</a>', $tweet['text']);
		echo '<p>';
		echo $tweet['text'];
		echo '<br /><small>' . date( $_config->DATEFORMAT, strtotime( $tweet['created_at'] ) ) . 
			' &ndash; <a href="http://twitter.com/'.$username.'/status/' . $tweet['id'] . '" target="_blank">Link</a></small>';
		echo '</p>';
		if( ++$j >= $count ) break;
	}
	echo '</div>';
}else{
	$_ms->add('Error Communicating with Twitter API, Error 2', true);
}

endif;