<?= DOCTYPE ?>
<html <?= HTMLATTR ?>>
<head>
	<?= co::layout('head') ?>

	<!-- Because IE7 borks -->
	<!--[if !IE 7]>
	<style>
	#topSort li, #sideSort li, #orphanage li, #legend li { filter:alpha(opacity=90); }
	</style>
	<![endif]-->

</head>
<body>
	<div id="containter">
		<?= co::layout('header') ?>
		<div id="content">
			<? $_ms->draw() ?>
			<?= $data ?>
		</div>
		<div id="footer"><p>&nbsp;</p><p>&nbsp;</p></div>
	</div>
</body>
</html>