<?

$_meta['name'] = 'GithubFeed';
$_meta['callable'] = true;

if( !$shutup ) :

$_config->defaults(
	array(
		'USERNAME' => 'donatj',
		'REPOSITORY' => 'CorpusPHP',
		'DIVCLASS' => 'GithubFeed',
		'DATEFORMAT'=> DISPLAY_DATE_FORMAT
	)
);

$USERNAME   = firstNotEmpty( $data['username'], $_config->USERNAME );
$REPOSITORY = firstNotEmpty( $data['repository'], $_config->REPOSITORY );

$opts = array( 'http' => array('header'  => 'User-Agent: Github Sucks at APIs') );
$context = stream_context_create($opts);
$feedUrl = "https://api.github.com/repos/{$USERNAME}/{$REPOSITORY}/commits";
$cacheKey = md5($feedUrl);

if( !$_cache->isExpired( $cacheKey ) ) {
	$feed = $_cache->$cacheKey;
}else{
	if( $feed = file_get_contents( $feedUrl, false, $context ) ) {
		$_cache->set( $cacheKey, $feed, 4, 'HOUR', false );
	}else{
		$feed = $_cache->$cacheKey;
		$_ms->add('Error Communicating with Github API, Error 1', true);
	}
}

$github = json_decode($feed, true);


echo '<div class="'. $_config->DIVCLASS .'"><h2>Recent Activity</h2>';
foreach( $github as $commit_full ) {
	$commit = $commit_full['commit'];
	$commit_date = $commit['committer']['date'];
	$date = date($_config->DATEFORMAT, strtotime( $commit_date ));
	if( $date != $dateHold ) {
		echo '<h3>' . co::module('blog/date', strtotime( $commit_date ) ) . '</h3>';
	}
	if( $commit_full['committer']['id'] != $authorHold ) {
		echo '<h4>' . $commit['committer']['name'] . ' <small>(<a href="https://github.com/'.$commit_full['committer']['login'].'" target="_blank">'. $commit_full['committer']['login'] .'</a>)</small></h4>';
	}
	echo nl2br( str_replace( "\t", '&nbsp;&nbsp;', htmlE($commit['message']) ) );
	echo '<hr />';
	$dateHold = $date;
	//drop($commit_full['committer']);
	$authorHold = $commit_full['committer']['id'];
}
echo '</div>';

endif;
