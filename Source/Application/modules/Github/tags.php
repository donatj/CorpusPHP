<?

$_meta['name'] = 'GithubTags';
$_meta['callable'] = true;

if( !$shutup ) :

$_config->defaults(
	array(
		'USERNAME' => 'donatj',
		'REPOSITORY' => 'CorpusPHP',
		'DATEFORMAT'=> DISPLAY_DATE_FORMAT
	)
);

$USERNAME   = firstNotEmpty( $data['username'], $_config->USERNAME );
$REPOSITORY = firstNotEmpty( $data['repository'], $_config->REPOSITORY );

$feedUrl = "http://github.com/api/v2/json/repos/show/{$USERNAME}/{$REPOSITORY}/tags";
$cacheKey = md5($feedUrl);

if( !$_cache->isExpired( $cacheKey ) ) {
	$feed = $_cache->$cacheKey;
}else{
	if( $feed = file_get_contents( $feedUrl ) ) {
		$_cache->set( $cacheKey, $feed, 1, 'DAY', false );
	}else{
		$feed = $_cache->$cacheKey;
		$_ms->add('Error Communicating with Github API, Error 1', true);
	}
}

$github = json_decode($feed, true);

krsort($github['tags']);
foreach( $github['tags'] as $tag => $hash ) {

	$feedUrl = "http://github.com/api/v2/json/commits/show/{$USERNAME}/{$REPOSITORY}/{$hash}";
	$cacheKey = md5($feedUrl);
	
	if( !$_cache->isExpired( $cacheKey ) ) {
		$feed = $_cache->$cacheKey;
	}else{
		if( $feed = file_get_contents( $feedUrl ) ) {
			$_cache->set( $cacheKey, $feed, 1, 'MONTH', true );
		}else{
			$feed = $_cache->$cacheKey;
			$_ms->add('Error Communicating with Github API, Error 1', true);
		}
	}
	
	$commit = json_decode($feed, true);
	
	$info['data'][] = array( 
		$tag . '<br /><small><a href="http://github.com/'. $USERNAME .'/' . $REPOSITORY . '/zipball/'.$tag.'">Download</a></small>',
		co::module('blog/date', strtotime($commit['commit']['committed_date']) ), 
		'<small>' . nl2br($commit['commit']['message']) . '</small>',
	);
	
}

$info['header'] = array('Build', 'Date', 'Message');
$info['params'] = 'class="datatable" style="width: 100%" cellpadding="0" cellspacing="0"';
echo co::module('table', $info);

endif;