<? 

$_meta['title'] = '404 Page';
$_meta['sitemap'] = false;

if( !$shutup ) :
	
header("HTTP/1.0 404 Not Found");
?>
<table width="100%">
<td width="100%">
	<h1>404 - Not Found</h1>
	<p>The page you requested could not be found</p>
</td>
<?

$_GET['search'] = str_replace('_',' ', $_meta['page']['url'] );
$_search_content = co::content('search', false, $m);
if( $m['search_results'] > 0 ) {
?>
<td>
<fieldset style="width: 300px;"><legend>Supplemental Results</legend>
<?= $_search_content ?>
</fieldset>
</td>
<? } ?>
</table>
	
<?
endif;