<?

$_meta['title'] = 'RSS Feed';
$_meta['sitemap'] = false;
$_meta['raw'] = true;
$_meta['execModuleCalls'] = false;

if( !$shutup ) {
	
	$fcfg = new Configuration( 'RSS Feed' );
	$fcfg->defaults(
		array(
			'FEEDIMAGE' => 'favicon.ico',
		)
	);
	
	header("Content-type: application/xml; charset=UTF-8");
	
	if( !function_exists('__rss_desc_cleanup') ) {
		function __rss_desc_cleanup( $string ) {
			$string = preg_replace('%<(?P<tagname>(?:iframe|script|style)).*?</?(?P=tagname)>%six', ' ', $string);
			$string = preg_replace_callback('/(?:href|src)="(.*?)"/six', 'href', $string);
			return $string;
		}
	}

	$doc = new DOMDocument('1.0');
	$doc->formatOutput = true;

	$rss = $doc->createElement('rss');
	$rss->setAttribute('version','2.0');
	$rss->setAttribute('xmlns:atom','http://www.w3.org/2005/Atom');
	$rss = $doc->appendChild($rss);

	$channel = $doc->createElement('channel');
	$channel = $rss->appendChild($channel);

	$channel->appendChild($doc->createElement('title', STORE_NAME ));
	$channel->appendChild($doc->createElement('link', DWS_BASE ));
	$channel->appendChild($doc->createElement('description', DEFAULT_META_DESC ));
	
	$self = $doc->createElement('atom:link');
	$self->setAttribute('rel', 'self');
	$self->setAttribute('type','application/rss+xml');
	$self->setAttribute('href',href());
	
	$channel->appendChild($self);

	$channel_image = $doc->createElement('image');
	$channel_image->appendChild($doc->createElement('url', href( $fcfg->FEEDIMAGE ) ));
	$channel_image->appendChild($doc->createElement('link', DWS_BASE ));
	$channel_image->appendChild($doc->createElement('title', STORE_NAME ));

	$channel->appendChild($channel_image);

	$qry = mysql_query("SELECT categories_id FROM categories WHERE list > 0 and template > 0 ORDER BY creation_date desc limit 75");
	while($row = mysql_fetch_array($qry)) {
		$_data = _::data( $row['categories_id'] );
		//print_r($_data);
		$item = $doc->createElement('item');
		$item->appendChild($doc->createElement('guid', href($row['categories_id']) ));
		//$item->appendChild($doc->createElement('title')->appendChild( $doc->createTextNode( $_data['name'] . ' ' ) ));
		$item->appendChild($doc->createElement('title'))->appendChild( $doc->createTextNode( $_data['name'] ) );
		$item->appendChild($doc->createElement('author',  STORE_GENERIC_FROM .'('. STORE_NAME .')' ) );
		$item->appendChild($doc->createElement('pubDate',  date("D, d M Y H:i:s", max( strtotime($_data['creation_date']), strtotime($_data['update_date']) ) ).' CST' ) );
		$item->appendChild($doc->createElement('link',  href($row['categories_id']) ) );
		self::exec_module_calls($_data['large_description']);
		$item->appendChild($doc->createElement('description'))->appendChild( $doc->createCDATASection( __rss_desc_cleanup( $_data['large_description']) ) );
		$channel->appendChild($item);
	}

	echo $doc->saveXML() . "\n";
	
}