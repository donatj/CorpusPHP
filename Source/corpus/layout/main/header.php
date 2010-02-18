<div id="header">
	<div id="header_inner">
		<a href="<?= DWS_BASE ?>"><img src="images/site/logo.png" alt="<?= STORE_NAME ?>" id="logo" /></a>
		<div id="headerbar">
			<?= draw_category_tree('header', 'nav') ?>
		</div>
		<?= breadcrumb( $_meta['id'] ) ?>
	</div>
</div>
<? $_ms->draw();