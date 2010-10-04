<?

if( !$shutup ) :

if( $_GET['drop'] > 0 ) {
	$_data = Core::data( (int)$_GET['drop'] );
	$_ms->add('Are you sure you want to delete the page &ldquo;'.$_data['name'].'&rdquo;?
		<p><a href="'.$_meta['page']['path'].'?drop_sure='.(int)$_GET['drop'].'">Yes</a>
		&mdash; <big><a href="'.$_meta['page']['path'].'">No</a></big></p>', 'dialog');
	_cu();
}

if( $_GET['drop_sure'] > 0 ) {
	$_data = Core::data( (int)$_GET['drop_sure'] );
	db::query("Delete From categories Where categories_id = " . (int)$_GET['drop_sure'] );
	db::query("Delete From images Where categories_id = " . (int)$_GET['drop_sure'] );
	$_ms->add('Successfully deleted the page &ldquo;'.$_data['name'].'&rdquo;');
	_cu();
}

if( count($_POST['sort']) > 0 ) {
	foreach($_POST['sort'] as $catid => $sort) {
		db::perform('categories', array('sort' => (int)$sort),'categories_id = '.(int)$catid);
	}
	$_ms->add('Sort Order Successfully Updated');
	_cu();
}

//$_ms->add('I shot the sherrif', 'dialog');

?>
<h1>Content Administration</h1>
<script type="text/javascript">
window.addEvent('domready', function() {
	$('bottomSticker').setStyle('display','none');

	$$('input.sort').addEvent('keyup', function(){
		$('bottomSticker').setStyle('display','block');
	});

});
</script>

<!-- Because IE7 borks -->
<!--[if !IE 7]>
<style>
#topSort li, #sideSort li, #orphanage li, #legend li { filter:alpha(opacity=90); }
</style>
<![endif]-->

<form method="post" action="<?= href() ?>">

	<table width="100%">
		<tr><td colspan="3"><h2 align="center">Databased Pages</h2></td></tr>
		<tr>
			<td width="50%">
				<h3>Side Navigation</h3>
				<?= draw_category_tree_admin('','sideSort',0,0) ?>
			</td>
			<td width="50%">
				<h3>Top Navigation</h3>
				<?= draw_category_tree_admin('','topSort',-1,0) ?>

				<h3>Orphanage</h3>
				<?= draw_category_tree_admin('','orphanage',-2,0) ?>
			</td>
			<td width="100">
				<h3>Legend</h3>
				<ul id="legend" style="text-align: right;">
					<li class="layout1">Page</li>
					<li class="layout-2">Redirect</li>
					<li class="layout3">Page Content</li>
					</ul>
				</td>
			</tr>
			<tr><td colspan="3"><h2 align="center">Databased Content Areas</h2></td></tr>
			<tr>
				<td>

					<h3>Application Content</h3>
					<ul class="app_content">
					<?

					$app_content = db::fetch("Select `key` From crux Where `key` like 'PAGECONTENT:%'", db::FLAT);
					foreach( $app_content as $key ) {
						?>
						<li class="layout-1">
							<div class="tray">
								<a href="admin/content/cruxedit.php?key=<?= urlencode( $key ) ?>"><img alt="edit" src="images/admin/edit.gif"></a>
							</div>
							<?= str_replace( 'PAGECONTENT:application/', '', $key ) ?>
						</li>
						<?
					}

					?>
					</ul>

				</td>
				<td>

					<h3>Content Blocks <small>(Crux)</small></h3>
					<ul class="app_content">
					<?

					$app_content = db::fetch("Select `key` From crux Where `key` Not Like '%:%' AND `key` Not Like 'IGSC%'", db::FLAT);
					foreach( $app_content as $key ) {
						?>
						<li class="layout-1">
							<div class="tray">
								<a href="admin/content/cruxedit.php?key=<?= urlencode( $key ) ?>"><img alt="edit" src="images/admin/edit.gif"></a>
							</div>
							<?
							$str = str_replace( 'PAGECONTENT:application/', '', $key );
							$str = str_replace( '_', ' ', $str );
							echo $str;
							?>
						</li>
						<?
					}

					?>
					</ul>

					</ul>

				</td>
			</tr>
			<tr>
				<td colspan="3">

					<h3>Application Page Top Descriptions</h3>
					<ul class="app_content" style="display: block;">
					<?

					$app_content = db::fetch("Select `key` From crux Where `key` like 'PAGEDESC:%'", db::FLAT);
					foreach( $app_content as $key ) {
						?>
						<li class="layout-1 thirdSize">
							<div class="tray">
								<a href="admin/content/cruxedit.php?key=<?= urlencode( $key ) ?>"><img alt="edit" src="images/admin/edit.gif"></a>
							</div>
							<?= str_replace( array('PAGEDESC:application/', '/'), array('','/<wbr>'), $key ) ?>
						</li>
						<?
					}

					?>

				</td>
			</tr>
	</table>

	<div id="bottomSticker">
		<input type="submit" value="Update Sort Order" />&nbsp;
	</div>

</form>

<?

endif;