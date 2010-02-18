<?= DOCTYPE ?>
<html <?= HTMLATTR ?>>
<head>
	<?= co::layout('head') ?>
</head>
<body>
	<?= co::layout('header') ?>
	<div id="wrap">
		<div id="pageBody">
			<?= $data ?>
			<div style="clear:both;"></div>
		</div>
	</div>
	<?= co::layout('footer') ?>
</body>
</html>