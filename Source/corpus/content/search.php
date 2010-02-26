<?

$_meta['title'] = 'Search Results';
$_meta['sitemap'] = false;

if( !$shutup ) :

	$searchA = keywordExpansion( $_GET['search'] );
	$search = implode(" ", $searchA);

	?>
	<h1>Search Results</h1>
	<?

	$ss = implodePre(" concat(name,description,large_description,details) like '%", $searchA, "%' OR ", "%'");

	$spr = new SplitPageResults("SELECT *, concat(name,description,large_description,details) as Data, Match(name,description,large_description,details) Against ( '".$search."' ) score
		From categories
		Where list = 1 AND (template > 0 OR template = -2) AND
		( Match(name,description,large_description,details) Against ( '".$search."' ) > 0
		OR " . $ss . " )
		Order By Data like '%".db::input($_GET['search'])."%' Desc, locate('".db::input($_GET['search'])."', Data) ASC, ( ".$ss." ) Desc, score Desc ", 8, 'search.php');
	
	$_meta['search_results'] = $spr->number_of_rows;
	$qry = db::query($spr->sql_query);
	
	if(mysql_numrows($qry) < 1 || strlen(trim($search)) < 2) {
		echo '<strong>Sorry, No Results for &ldquo;' . htmlE($_GET['search']) . '&rdquo;</strong>';
	}else{

		while( $row = mysql_fetch_array( $qry ) ) {

			$content = $row['large_description'] . "\n" . $row['description'] . "\n" . $row['details'];
			$content = str_replace('&nbsp;', ' ', $content);
			$content = strip_tags( str_replace("<"," <",$content) );
			$content = nl2br( trim( substr( $content, max( stripos($content, $_GET['search']) - 45, 0 ) ,300) ) );
			$content = preg_replace('/(<br\s*\/?>\s*)+/i','<br />', $content);
			$content = preg_replace(PATTERN_MODULE_CALL, '<!-- Removed Module -->', $content);

			foreach($searchA as $s) {
				$content = str_ireplace( $s, '<strong>' . $s . '</strong>', $content );
			}

			echo '<h3><a href="'.href( $row['categories_id'] ).'">' . $row['name'] . '</a></h3>';
			echo '<div>' . $content . '…</div><hr>';
		}

		echo '<br />';
		echo '<div align="center">';
		echo $spr->getLinks( getvarsSerializer( 'page', 'url') );
		echo '</div>';

	}

endif;