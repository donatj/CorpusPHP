<?

if( !$shutup ) :

$tags = db::fetch('select *
from
	(
		select
			(@counter :=@counter + 1)& 1 as x,
			w.*
		FROM
			(SELECT @counter := 0)v,
			(
				select
					tag,
					count(*)as c
				from
					categories_tags
				inner join categories using(categories_id)
				where
					`status`
				and `list`
				group by
					tag
				order by
					c
			)w
	)x
order by
	x, if(x, - c, c), if(x, -CHAR_LENGTH(tag), CHAR_LENGTH(tag))');

//print_r( $tags );
echo '<div class="TagCloudWrap">';
foreach( $tags as $tag ) {
	echo '<a style="font-size: '. number_format(($tag['c'] / 3) + .5, 3) .'em" href="'. href('tags?tag=' . urlencode($tag['tag'])) .'">' . $tag['tag'] . '</a> ';
}
echo '</div>';

endif;
