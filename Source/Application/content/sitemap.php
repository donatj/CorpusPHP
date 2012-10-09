<?

$_meta['title'] = 'Site Map';
$_meta['sitemap'] = false;

if( !$shutup ) {
?>
<h1>Site Map</h1>
<ul>

	<li>Site Navigation
	<?= draw_category_tree('','',-1) ?>
	<ul>
	<?
	$info = co::content_info();
	foreach($info as $file => $data) {
		if($data['sitemap'] !== false) {
			echo '<li><a href="'.htmlE($file).'">' . htmlE( firstNotEmpty( $data['sitemap'], $data['title'] ) ) . '</a></li>';
		}
	}
	?>
	</ul>
	</li>
	<?= draw_category_tree('','') ?>
</ul>
<?
}