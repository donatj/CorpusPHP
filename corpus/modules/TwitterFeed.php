<?

$_meta['call'] = 'TwitterFeed';

if( !$shutup ) :

$username = urlencode( firstNotEmpty( $data[0], 'donatj' ) );
$count = (int)firstNotEmpty( $data[1], 10 );


$feedUrl = 'http://twitter.com/statuses/user_timeline/' . $username . '.json?count=' . $count;
$cacheKey = md5($feedUrl);

if( !Cache::isExpired( 'TwitterFeed', $cacheKey ) ) {
	$feed = Cache::get( 'TwitterFeed', $cacheKey );
}else{
	if( $feed = file_get_contents( $feedUrl ) ) {
		Cache::set( 'TwitterFeed', $cacheKey, $feed, 15, 'MINUTE', false );
	}else{
		$feed = Cache::get( 'TwitterFeed', $cacheKey );
		$_ms->add('Error Communicating with Twitter API, Error 1', true);
	}
}

$twitter = json_decode( $feed, true );
if( is_array( $twitter ) ) {
	echo '<div class="TwitterFeedWrap">';
	foreach( $twitter as $tweet ) {
		$tweet['text'] = preg_replace('/([A-Za-z][A-Za-z0-9+.-]{1,120}:[A-Za-z0-9\/](([A-Za-z0-9$_.+!*,;\/?:@&~=-])|%[A-Fa-f0-9]{2}){1,333}(#([a-zA-Z0-9][a-zA-Z0-9$_.+!*,;\/?:@&~=%-]{0,1000}))?)/', '<a href="$1" target="_blank" class="TwitterLink TwitterUser">$1</a>', $tweet['text']);
		$tweet['text'] = preg_replace('/@([A-Za-z0-9_]+)/', '<a href="http://twitter.com/$1" target="_blank" class="TwitterLink TwitterUser">@$1</a>', $tweet['text']);
		echo '<p>';
		echo $tweet['text'];
		echo '<br /><small>' . date( 'F j, Y, g:i a', strtotime( $tweet['created_at'] ) ) . 
			' &ndash; <a href="http://twitter.com/'.$username.'/status/' . $tweet['id'] . '" target="_blank">Link</a></small>';
		echo '</p>';
	}
	echo '</div>';
}else{
	$_ms->add('Error Communicating with Twitter API, Error 2', true);
}

endif;