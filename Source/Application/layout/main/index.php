<!DOCTYPE html>
<html lang="en">
<head>
	<?= co::layout('head') ?>
</head>
<body>
	<?= co::layout('header') ?>
	<div id="wrap">
		<div class="inner">
			<?= $data ?>
			<div style="clear:both;"></div>
		</div>
	</div>
	<?= co::layout('footer') ?>
</body>
</html>