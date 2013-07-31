<?php

$_meta['name']     = 'GithubTags';
$_meta['callable'] = true;

if( !$shutup ) :

	$this->_config->defaults(
		array(
			'USERNAME'   => 'donatj',
			'REPOSITORY' => 'CorpusPHP',
			'DATEFORMAT' => DISPLAY_DATE_FORMAT
		)
	);

	$USERNAME   = firstNotEmpty($this->data['username'], $this->_config->USERNAME);
	$REPOSITORY = firstNotEmpty($this->data['repository'], $this->_config->REPOSITORY);

	$opts    = array( 'http' => array( 'header' => 'User-Agent: Github Sucks at APIs' ) );
	$context = stream_context_create($opts);
	$feedUrl = "https://api.github.com/repos/{$USERNAME}/{$REPOSITORY}/git/refs/tags";

	$cacheKey = md5($feedUrl);

	if( !$this->_cache->isExpired($cacheKey) ) {
		$feed = $this->_cache->$cacheKey;
	} else {
		if( $feed = file_get_contents($feedUrl, false, $context) ) {
			$this->_cache->set($cacheKey, $feed, 1, 'DAY', false);
		} else {
			$feed = $this->_cache->$cacheKey;
			$_ms->add('Error Communicating with Github API, Error ' . __LINE__, true);
		}
	}

	$github = json_decode($feed, true);

	$info = array( 'data' => array() );

	foreach( $github as $this->data ) {
		$feedUrl = $this->data['object']['url'];

		$tag = str_replace('refs/tags/', '', $this->data['ref']);

		$cacheKey = md5($feedUrl);

		if( !$this->_cache->isExpired($cacheKey) ) {
			$feed = $this->_cache->$cacheKey;
		} else {
			if( $feed = @file_get_contents($feedUrl) ) {
				$this->_cache->set($cacheKey, $feed, 1, 'MONTH', true);
			} else {
				$feed = $this->_cache->$cacheKey;
				$_ms->add('Error Communicating with Github API, Error ' . __LINE__ . ':' . $this->data['ref'] . ' - ' . $hash, true);
			}
		}

		$commit = json_decode($feed, true);

		$download_link = 'http://github.com/' . $USERNAME . '/' . $REPOSITORY . '/zipball/' . $tag;
		if( $commit ) {
			array_unshift($info['data'], array(
				$tag . '<br /><small><a href="' . $download_link . '" target="_blank">Download</a></small>',
				$commit['tagger']['date'] ? co::module('blog/date', strtotime($commit['tagger']['date'])) : '',
				'<small>' . nl2br($commit['message']) . '</small>',
			));
		}
	}

	$info['header'] = array( 'Build', 'Date', 'Message' );
	$info['params'] = 'class="datatable" style="width: 100%" cellpadding="0" cellspacing="0"';
	echo co::module('table', $info);

endif;