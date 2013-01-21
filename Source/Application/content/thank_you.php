<?php
//the nessessary thanks

$_meta['title'] = 'Thank You';
$_meta['sitemap'] = false;

if( !$shutup ) {
	
	$thanks_stack = new MessageStack('thanks');

	$thanks_stack->draw();
	?>
	<br /><br />
	<div align="center"><a href="<?= DWS_BASE ?>">Return Home</a></div>
<?php

}