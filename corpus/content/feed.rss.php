<?

$_meta['title'] = 'RSS Feed';
$_meta['sitemap'] = false;
$_meta['raw'] = true;

if( !$shutup ) {
	header("Content-type: application/xml; charset=UTF-8");

	$doc = new DOMDocument('1.0');
	$doc->formatOutput = true;

	$rss = $doc->createElement('rss');
	$rss->setAttribute('version','2.0');
	$rss = $doc->appendChild($rss);

	$channel = $doc->createElement('channel');
	$channel = $rss->appendChild($channel);

	$channel->appendChild($doc->createElement('title', STORE_NAME ));
	$channel->appendChild($doc->createElement('link', DWS_BASE ));
	$channel->appendChild($doc->createElement('description', DEFAULT_META_DESC ));

	$channel_image = $doc->createElement('image');
	$channel_image->appendChild($doc->createElement('url', DWS_BASE . 'favicon.ico' ));
	$channel_image->appendChild($doc->createElement('link', DWS_BASE ));
	$channel_image->appendChild($doc->createElement('title', STORE_NAME ));

	$channel->appendChild($channel_image);

	$qry = mysql_query("SELECT categories_id FROM categories WHERE list > 0 and template > 0 ORDER BY categories_id desc limit 75");
	while($row = mysql_fetch_array($qry)) {
		$_data = _::data( $row['categories_id'] );
		//print_r($_data);
		$item = $doc->createElement('item');
		$item->appendChild($doc->createElement('guid', href($row['categories_id']) ));
		//$item->appendChild($doc->createElement('title')->appendChild( $doc->createTextNode( $_data['name'] . ' ' ) ));
		$item->appendChild($doc->createElement('title'))->appendChild( $doc->createTextNode( $_data['name'] ) );
		$item->appendChild($doc->createElement('author',  STORE_GENERIC_FROM .'('. STORE_NAME .')' ) );
		//$item->appendChild($doc->createElement('pubDate',  date("D, d M Y H:i:s", strtotime($row['post_date']) ).' CST' ) );
		$item->appendChild($doc->createElement('link',  href($row['categories_id']) ) );
		$item->appendChild($doc->createElement('description'))->appendChild( $doc->createCDATASection( $_data['large_description'] ) );
		$channel->appendChild($item);
	}

	echo $doc->saveXML() . "\n";
}