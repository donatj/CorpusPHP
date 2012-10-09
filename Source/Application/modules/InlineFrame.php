<?

$_meta['name'] = 'IFrame';
$_meta['callable'] = true;

if( !$shutup ) :

$_config->defaults(
	array(
		'SRC' => 'http://donatstudios.com',
		'WIDTH' => 400,
		'HEIGHT' => 400,
	)
);

$width = firstNotEmpty( $data['width'], $_config->WIDTH );
if( is_numeric($width) ) { $width = (int)$width . 'px'; }

$height = firstNotEmpty( $data['height'], $_config->HEIGHT );
if( is_numeric($height) ) { $height = (int)$height . 'px'; }

echo '<iframe frameBorder="0" src="'.firstNotEmpty( $_config->src, $data['src'] ).'" style="width:'.$width.';height:'.$height.'"></iframe>';

endif;