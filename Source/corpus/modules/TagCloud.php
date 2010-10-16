<?

if( !$shutup ) :

$tags = db::fetch('select tag, count(*) as c from categories_tags inner join categories using(categories_id) where `status` and `list` group by tag order by tag');
//print_r( $tags );
echo '<div class="TagCloudWrap">';
foreach( $tags as $tag ) {
	echo '<a style="font-size: '. (($tag['c'] / 2) + .5) .'em" href="'. href('tags?tag=' . htmlE($tag['tag'])) .'">' . $tag['tag'] . '</a> ';
}
echo '</div>';

endif;
