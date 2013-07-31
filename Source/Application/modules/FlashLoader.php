<?php

$_meta['name'] = 'FlashLoader';
$_meta['callable'] = true;

if( !$shutup ) :

	$id = 'FlashLoader' . uniqid();
	?>
	<div class="flashload" id="<?= $id ?>">Placeholder For <?= $this->data[0] ?></div>
	<script type="text/javascript">
		window.addEvent('load', function () {
			new Swiff('flash/<?= $this->data[0] ?>', {'container':<?= json_encode($id) ?>, 'width':<?= (int)$this->data[1] ?>, 'height':<?= (int)$this->data[2] ?>});
		});
	</script>
<?

endif;