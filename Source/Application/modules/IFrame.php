<?php

$this->_config->defaults(
	array(
		'SRC'    => 'http://donatstudios.com',
		'WIDTH'  => 400,
		'HEIGHT' => 400,
	)
);

$width = firstNotEmpty($this->data['width'], $this->_config->WIDTH);
if( is_numeric($width) ) {
	$width = (int)$width . 'px';
}

$height = firstNotEmpty($this->data['height'], $this->_config->HEIGHT);
if( is_numeric($height) ) {
	$height = (int)$height . 'px';
}

echo '<iframe frameBorder="0" src="' . firstNotEmpty($this->_config->src, $this->data['src']) . '" style="width:' . $width . ';height:' . $height . '"></iframe>';
