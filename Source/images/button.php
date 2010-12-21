<?
//image
$base_file = 'site/button.png';
$side_pad = 15;

//text
$font = 'fonts/Vera.ttf';
$font_size = 10;
$font_color = '#211';
$font_alpha = 30; //0-127, somewhere around 60 makes for a nice blend

list($base_width, $base_height) = getimagesize($base_file);
$base_image = imagecreatefromstring( file_get_contents( $base_file ) );

//text
$text = stripslashes(urldecode($_GET['text']));
$text_size_arr = imagettfbbox($font_size, 0, $font, $text);
$text_width = $text_size_arr[2] - $text_size_arr[0];
$text_size_arr = imagettfbbox($font_size, 0, $font, 'ABC'); //ensures no downward letters like j
$text_height = $text_size_arr[1] - $text_size_arr[5];

$width = $text_width + $side_pad * 2;
$img = imagecreatetruecolor($width, $base_height);
imagesavealpha($img, true);
$transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
imagefill($img, 0, 0, $transparent);

//set up background
imagecopyresized( $img, $base_image, $base_width / 2, 0, $base_width / 2, 0, $width - $base_width, $base_height, 1, $base_height);
imagecopy($img, $base_image, 0,0,0,0,$base_width / 2, $base_height);
imagecopy($img, $base_image, $width - $base_width / 2,0,$base_width / 2,0,$base_width / 2 + 1, $base_height);

list($fr,$fg,$fb) = html2rgb($font_color);

//shadow
$font_color = imagecolorallocatealpha($img, $fr, $fg, $fb, 90);
imagettftext($img, $font_size, 0, $side_pad + 1, ($text_height / 2) + ($base_height / 2) + 1, $font_color, $font, $text);

//text
$font_color = imagecolorallocatealpha($img, $fr, $fg, $fb, $font_alpha);
imagettftext($img, $font_size, 0, $side_pad, ($text_height / 2) + ($base_height / 2), $font_color, $font, $text);

header('Cache-Control: public');
header('Last-Modified: '.gmdate('D, d M Y H:i:s', strtotime("-1 Week")) . ' GMT');
header('Expires: '.gmdate('D, d M Y H:i:s', strtotime("+2 Week")) . ' GMT');
header ('Content-type: image/png');
imagepng($img);

function html2rgb($color){
	if ($color[0] == '#'){ $color = substr($color, 1); }

	if (strlen($color) == 6){
		list($r, $g, $b) = array($color[0].$color[1], $color[2].$color[3], $color[4].$color[5]);
	}elseif (strlen($color) == 3){
		list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
	}else{ return false; }
	$red = hexdec($r); $green = hexdec($g); $blue = hexdec($b);
	return array($red, $green, $blue);
}