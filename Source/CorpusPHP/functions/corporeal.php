<?
function crux( $key ) {
	return db::fetch( "Select value From crux where `key` = '" . db::input( $key ) . "'", db::SCALAR );
}

function recrux( $key, $value ) {
	return db::perform('crux', array('key' => $key, 'value' => $value), true);
}

function addressFormat($data, $html=true) {
	$str = '';
	if( nempty($data['address']) ) $str .= $data['address'] . "\n";
	if( nempty($data['address2']) ) $str .= $data['address2'] . "\n";
	if( nempty($data['city']) ) $str .= $data['city'] . ", ";
	if( nempty($data['state']) ) $str .= $data['state'];
	if( nempty($data['zip']) ) $str .= "\n" . $data['zip'];
	if( $html ) return nl2br($str);
	return $str;
}

function draw_category_tree($class_prefix = "nav_", $root_id = "nav", $cat_id = 0, $depth = 0, &$child_selected = false, $cat_data = false) {
	global $_id;

	if( $cat_data === false ) {
		$cat_data = category_struct();
	}

	$src = '';
	if(is_array( $cat_data[$cat_id]['children'] )) {
		$src .= '<ul'.($depth == 0 ? ' id="'.$root_id.'"' : '').' class="'.$class_prefix. (int)$depth.'">';
		foreach( $cat_data[$cat_id]['children'] as $cat ) {
			if(!$cat['data']['list'] || !$cat['data']['status']) { continue; }
			$child_selected_sub = false;

			$csrc = draw_category_tree( $class_prefix, $root_id, $cat['data']['categories_id'], $depth + 1, $child_selected_sub,  $cat_data[$cat_id]['children'] );
			$child_selected |= ( $selected = ( $cat['data']['categories_id'] == $_id || $child_selected_sub ) );

			$src .= PHP_EOL . str_repeat("\t", $depth) . '<li class="'. ( $selected ? 'selected' : '') .'">';
			$src .= '<a href="' .href($cat['data']['categories_id']) . '">'; 
			$src .= $cat['data']['name'];
			$src .= '</a>';

			$src .= $csrc;
			$src .= '</li>';
		}
		$src .= '</ul>';
	}
	return $src;
}

function category_struct() {
	static $data = false;

	if( $data === false ) {
		$data = array();	
		$cats = db::fetch("SELECT
			c.categories_id, c.name, c.parent_id, c.redirect, c.template, c.status, c.list
		FROM
			categories c
		GROUP BY
			c.categories_id
		ORDER BY
			c.parent_id, c.sort, c.name");

		foreach($cats as $row) {	
			$data[ $row['categories_id'] ]['data'] = $row;
			$data[ $row['parent_id'] ]['children'][ $row['categories_id'] ] =& $data[ $row['categories_id'] ];
	  		$data[ $row['categories_id'] ]['parent'] =& $data[ $row['categories_id'] ];
	 	}
 	}
	return $data;
}
  

function getParent( $id ) {
	$qry = db::query("Select parent_id From categories Where categories_id = " . (int)$id);
	$row = mysql_fetch_array($qry);
	if( $row['parent_id'] > 0 ) {
		return (int)$row['parent_id'];
	}
	return false;
}

function breadcrumb( $id, $seperator = ' &raquo; ' ) {
	//if( $id < 1 ) return false;
	global $_meta;
	if( !$_meta['hide_breadcrumb'] ) {
		$id2 = $id;
		if($id != 0) {
			do {
				$qry = db::query("Select parent_id, name, template From categories Where categories_id = " . (int)$id2);
				$row = mysql_fetch_array($qry);
				if($id2 == $id) {
					$data[] = '<strong>' . $row['name'] . '</strong>';
				}else{
					if($id2 > 0) {
						$data[] = '<a itemprop="url" href="'.href($id2).'" class="template'.(int)$row['template'].'"><span itemprop="title">' . $row['name'] . '</span></a>';
					}
				}
			} while( $id2 = $row['parent_id'] );
		}
		$data = firstNotEmpty($_meta['breadcrumbs'], $data, array('<strong>' . $_meta['title'] . '</strong>'));
		$data[] = '<a itemprop="url" href="'.DWS_BASE.'"><span itemprop="title">Home</span></a>';
		$data = array_reverse($data);
		$i = count($data);
		return '<div class="breadcrumb" itemscope itemtype="http://data-vocabulary.org/Breadcrumb">' . implode( $seperator, $data ) . '</div>';
	}
}

function button( $text, $link = false, $linkParams = '', $buttonParams = '', $type = false ) {
	if( $type ) {
	}elseif( is_string($link) || $link === false ) {
		$type = 'button';
	}else{
		$type = 'submit';
	}

	$return = '<button type="' . $type . '" '.$buttonParams.'>'.htmlE( $text ).'</button>';
	if($link && $link !== true ) { $return = '<a title="' . htmlE($text) . '" href="'.href($link).'" '.$linkParams.'>' . $return . '</a>'; }
	return $return;
}

function keywordExpansion( $searchedFor ) {
	$inf = new Inflector();

	$search = trim( $searchedFor );
	$searchA = explode(' ',$search);
	$searchA2 = array();
	foreach($searchA as &$s) {
		if(strlen(trim($s))) {
			$searchA2[] = db::input($inf->pluralize($s));
			$searchA2[] = db::input($inf->singularize($s));
			$searchA2[] = db::input($s);
		}
	}
	return array_unique($searchA2);
}

/**
* Oxford style comma seperates
* 
* I climbed to Dharamsala
* I met the highest lama
* His accent sounded fine
* To me
* 
* @param array $texts array of texts to be oxford commafied
* @param string $conj the conjunction
* @param string $sep the separator
*/
function OxfordComma( $texts, $conj = 'and', $sep = ',' ) {
	$len = count( $texts );
	$str = '';
	$j   = 0;
	foreach( $texts as $fragment ) {
		$str .= $fragment;
		//echo $len;
		if( $len - 1 > ++$j ) {
			$str .= $sep .  ' ';
		}elseif( $len > $j ){
			if( $len > 2 ) { $str .= $sep; }
			$str .= ' ' . $conj .  ' ';
		}
	}
	return $str;
}