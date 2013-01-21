<?php

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

$feedUrl = "http://github.com/api/v1/json/{$USERNAME}/{$REPOSITORY}/commits/master";
$cacheKey = md5($feedUrl);

if( !$_cache->isExpired( $cacheKey ) ) {
	$feed = $_cache->$cacheKey;
}else{
	if( $feed = file_get_contents( $feedUrl ) ) {
		$_cache->set( $cacheKey, $feed, 4, 'HOUR', false );
	}else{
		$feed = $_cache->$cacheKey;
		$_ms->add('Error Communicating with Github API, Error 1', true);
	}
}

$github = json_decode($feed, true);

echo '<div class="'. $_config->DIVCLASS .'"><h2>Recent Activity</h2>';
foreach( $github['commits'] as $commit ) {
	$date = date($_config->DATEFORMAT, strtotime($commit['committed_date']));
	if( $date != $dateHold ) {
		echo '<h3>' . co::module('blog/date', strtotime($commit['committed_date']) ) . '</h3>';
	}
	echo nl2br( str_replace( "\t", '&nbsp;&nbsp;', htmlE($commit['message']) ) );
	echo '<hr />';
	$dateHold = $date;
}
echo '</div>';

endif;
