<?

if( !$shutup ) :
$_meta['execModuleCalls'] = false;

if( $_POST['crux'] && $_GET['key'] ) {
	if( db::perform( 'crux', array( 'value' => $_POST['crux'] ), '`key` = "' . db::input( $_GET['key'] ) . '"' ) ) {
		$_ms->add('Content Area Successfully Updated');
		redirect('admin/content');
	}
}

$_meta['header'] = '
<script type="text/javascript" src="js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	content_css: "css/tiny_mce.css",
	document_base_url : '.json_encode(DWS_BASE).'
});
</script>';

?>

<h1>Content Blocks - Editing <?= $_GET['key'] ?></h1>
<fieldset>
	<form action="<?= href() ?>?key=<?= urlencode($_GET['key']) ?>" method="post">
	
	<?= fe::Textarea( 'crux', crux( $_GET['key'] ), 'style="width: 100%; height: 300px"' ) ?>

	<hr />
	
	<center>
		<?= button('Update', true) ?>
		<?= button('Cancel', 'admin/content/crux') ?>
	</center>
<?	
	echo '<fieldset style="width: 200px; float: right; margin-top: -30px"><legend>Callable Modules</legend>';
	echo '<ul>';
	foreach( Corpus::$metaSupreme['modules']['meta'] as $module ) {
		if( $module['name'] && $module['callable'] ) {
			echo "<li>%{$module['name']}[]%</li>";
		}
	}
	echo '<ul></fieldset>';
?>
	
	</form>
</fieldset>

<?
endif;